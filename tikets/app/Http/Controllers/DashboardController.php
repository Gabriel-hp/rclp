<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private $apiUrl = "https://api.movidesk.com/public/v1/tickets";
    private $apiToken = "8ef83eef-7ee0-4629-8d6d-ed0680eee679";

    private function buscarChamadosAPI()
    {
        $params = [
            'token' => $this->apiToken,
            '$select' => 'protocol,status,ownerTeam,createdDate',
            '$filter' => "status eq 'Em atendimento' or status eq 'Aguardando'",
            '$expand' => 'clients($select=businessName)', 
        ];

        $response = Http::get($this->apiUrl, $params);

        if (!$response->successful()) {
            return null;
        }

        return collect($response->json())->map(function ($ticket) {
            $createdDate = $ticket['createdDate'] ?? null;
            $abertoEm = $createdDate ? Carbon::parse(preg_replace('/\..+/', '', $createdDate)) : null;
            $tempoAberto = $abertoEm ? $abertoEm->diffInSeconds(Carbon::now()) : null;
            $cliente = !empty($ticket['clients']) && is_array($ticket['clients']) ? $ticket['clients'][0]['businessName'] ?? 'Cliente' : 'Cliente';

            return [
                'protocolo' => $ticket['protocol'] ?? 'N/A',
                'cliente' => $cliente,
                'status' => $ticket['status'] ?? 'Desconhecido',
                'nivel' => $this->getNivel($ticket['ownerTeam'] ?? 'Indefinido'),
                'tempoAberto' => $tempoAberto,
                'abertoEm' => $abertoEm,
            ];
        })->toArray();
    }

    public function atualizarChamados()
    {
        $tickets = $this->buscarChamadosAPI();
        session(['chamados' => $tickets]); 
        return response()->json($tickets);
    }


    public function index(Request $request)
    {

        $tickets = $this->buscarChamadosAPI();

        if (!$tickets) {
            return view('dashboard')->with('erro', 'Erro ao buscar chamados');
        }

        $chamadosCollection = collect($tickets);

        // Aplicação de filtros
        if ($request->filled('numero_chamado')) {
            $chamadosCollection = $chamadosCollection->filter(fn($ticket) => strpos($ticket['protocolo'], $request->numero_chamado) !== false);
        }

        if ($request->filled('status')) {
            $chamadosCollection = $chamadosCollection->where('status', $request->status);
        }

        if ($request->filled('periodo')) {
            $dataLimite = now()->subDays($request->periodo);
            $chamadosCollection = $chamadosCollection->filter(fn($chamado) => $chamado['abertoEm'] && $chamado['abertoEm']->greaterThanOrEqualTo($dataLimite));
        }

        if ($request->filled('nivel')) {
            $chamadosCollection = $chamadosCollection->where('nivel', $request->nivel);
        }

        // Ordenação
        $chamadosCollection = $chamadosCollection->sortByDesc(fn($chamado) => $chamado['tempoAberto']);

        if ($request->ordenacao == 'antigo') {
            $chamadosCollection = $chamadosCollection->sortBy(fn($chamado) => $chamado['tempoAberto']);
        }

        // Paginação manual (100 por página)
        $chamadosPaginados = $chamadosCollection->forPage($request->input('page', 1), 100);

        // Contagem de chamados por status e nível
        $statusCount = $this->contarChamadosPorStatusENivel($chamadosCollection);

        return view('dashboard', compact('chamadosPaginados', 'statusCount', 'chamadosCollection'));
    }

    private function contarChamadosPorStatusENivel($collection)
    {
        $statusTypes = ['Em atendimento', 'Aguardando'];
        $niveis = ['Junior', 'Pleno', 'Senior'];
        $statusCount = [];

        foreach ($statusTypes as $status) {
            $statusCount[$status] = $collection->where('status', $status)->count();
            foreach ($niveis as $nivel) {
                $statusCount["{$status} {$nivel}"] = $collection->where('status', $status)->where('nivel', $nivel)->count();
            }
        }

        return $statusCount;
    }

    private function getNivel($ownerTeam)
    {
        $mapaNiveis = [
            'Suporte - Técnico Junior' => 'Junior',
            'Suporte - Técnico Pleno' => 'Pleno',
            'Suporte - Técnico Sênior' => 'Senior',
            'Data Center' => 'Data Center',
            'Operações' => 'Operações',
        ];

        foreach ($mapaNiveis as $chave => $nivel) {
            if (strpos($ownerTeam, $chave) !== false) {
                return $nivel;
            }
        }

        return 'Indefinido';
    }
}
