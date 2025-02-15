<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private $apiUrl = "https://api.movidesk.com/public/v1/tickets";
    private $apiToken = "8ef83eef-7ee0-4629-8d6d-ed0680eee679"; // Substitua pelo seu token

    public function index(Request $request)
    {
        // Busca os chamados na API
        $tickets = $this->buscarChamadosAPI();

        if (!$tickets) {
            return view('dashboard')->with('erro', 'Erro ao buscar chamados');
        }

        // Converte os tickets em uma coleção
        $chamadosCollection = collect($tickets);

        // 🔹 Aplicação de filtros
        if ($request->filled('numero_chamado')) {
            $chamadosCollection = $chamadosCollection->filter(function ($ticket) use ($request) {
                return strpos($ticket['protocolo'], $request->numero_chamado) !== false;
            });
        }

        if ($request->filled('status')) {
            $chamadosCollection = $chamadosCollection->where('status', $request->status);
        }

        if ($request->filled('periodo')) {
            $chamadosCollection = $chamadosCollection->filter(function ($ticket) use ($request) {
                return strtotime($ticket['abertoEm']) >= now()->subDays($request->periodo)->timestamp;
            });
        }

        if ($request->filled('nivel')) {
            $chamadosCollection = $chamadosCollection->where('nivel', $request->nivel);
        }

        // Ordenação
        if ($request->ordenacao == 'recente') {
            $chamadosCollection = $chamadosCollection->sortByDesc('abertoEm');
        } elseif ($request->ordenacao == 'antigo') {
            $chamadosCollection = $chamadosCollection->sortBy('abertoEm');
        }

        // 🔹 Paginação manual (15 por página)
        $chamadosPaginados = $chamadosCollection->forPage($request->input('page', 1), 50);

        // 🔹 Contagem de chamados por status e nível
        $statusCount = [
            'Em Aberto' => $chamadosCollection->where('status', 'Em atendimento')->count(),
            'Aguardando' => $chamadosCollection->where('status', 'Aguardando')->count(),

            'Em Aberto Junior' => $chamadosCollection->where('status', 'Em atendimento')->where('nivel', 'Junior')->count(),
            'Aguardando Junior' => $chamadosCollection->where('status', 'Aguardando')->where('nivel', 'Junior')->count(),

            'Em Aberto Pleno' => $chamadosCollection->where('status', 'Em atendimento')->where('nivel', 'Pleno')->count(),
            'Aguardando Pleno' => $chamadosCollection->where('status', 'Aguardando')->where('nivel', 'Pleno')->count(),

            'Em Aberto Sênior' => $chamadosCollection->where('status', 'Em atendimento')->where('nivel', 'Sênior')->count(),
            'Aguardando Sênior' => $chamadosCollection->where('status', 'Aguardando')->where('nivel', 'Sênior')->count(),
        ];

        return view('dashboard', compact('chamadosPaginados', 'statusCount', 'chamadosCollection'));
    }

    private function buscarChamadosAPI()
    {
        // Parâmetros da requisição
        $params = [
            'token' => $this->apiToken,
            '$select' => 'subject,protocol,status,ownerTeam,createdDate',
            '$filter' => "status eq 'Em atendimento' or status eq 'Aguardando'"

        ];

        // Faz a requisição à API
        $response = Http::get($this->apiUrl, $params);
        if ($response->successful()) {
            return collect($response->json())->map(function ($ticket) {
    
                return [
                    'protocolo' => $ticket['protocol'] ?? 'N/A',
                    'assunto' => $ticket['subject'] ?? 'Sem Assunto',
                    'status' => $ticket['status'] ?? 'Desconhecido',
                    'nivel' => $this->getNivel($ticket['ownerTeam'] ?? 'Indefinido') 
                ];
            })->toArray();
        }

        return null;
    }

    private function getNivel($ownerTeam)
    {
        if (strpos($ownerTeam, 'Suporte - Técnico Junior') !== false) {
            return 'Junior';
        } elseif (strpos($ownerTeam, 'Suporte - Técnico Pleno') !== false) {
            return 'Pleno';
        } elseif (strpos($ownerTeam, 'Suporte - Técnico Sênior ') !== false) {
            return 'Sênior';
        }
        elseif (strpos($ownerTeam, 'Data Center') !== false) {
            return 'Data Center';
        }
        elseif (strpos($ownerTeam, 'Operações') !== false) {
            return 'Operações';
        }

        return 'Indefinido'; 
    }
}