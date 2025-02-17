<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private $apiUrl = "https://api.movidesk.com/public/v1/tickets";
    private $apiToken = "8ef83eef-7ee0-4629-8d6d-ed0680eee679"; // Substitua pelo seu token
    
    private function buscarChamadosAPI()
    {
        // Parâmetros da requisição
        $params = [
            'token' => $this->apiToken,
            '$select' => 'protocol,status,ownerTeam,createdDate',
            '$filter' => "status eq 'Em atendimento' or status eq 'Aguardando'",
            '$expand' => 'clients($select=businessName)', 
        ];

        // Faz a requisição à API
        $response = Http::get($this->apiUrl, $params);
        if ($response->successful()) {
            return collect($response->json())->map(function ($ticket) {
               // Verifica se a chave 'createdDate' existe no array
            $createdDate = $ticket['createdDate'] ?? null;

            // Se a data de criação estiver disponível, processa e formata
            if ($createdDate) {
                // Remove possíveis "trailing data" da string de data
                $createdDate = preg_replace('/\..+/', '', $createdDate); // Remove frações de segundos e dados extras

                // Converte a data de abertura para o formato ISO 8601
                $abertoEm = Carbon::parse($createdDate);

                // Calcula o tempo aberto
                $tempoAberto = $abertoEm->diff(Carbon::now());


            $cliente = 'Cliente'; 
            if (!empty($ticket['clients']) && is_array($ticket['clients'])) {
                $cliente = $ticket['clients'][0]['businessName'] ?? 'Cliente'; 
            }
            } else {
  
                $abertoEm = null;
                $tempoAberto = null;
            }
 
                return [
                    'protocolo' => $ticket['protocol'] ?? 'N/A',
                    'cliente' => $cliente, // Adiciona o nome do cliente ao array
                    'status' => $ticket['status'] ?? 'Desconhecido',
                    'nivel' => $this->getNivel($ticket['ownerTeam'] ?? 'Indefinido'),
                    'tempoAberto' => $tempoAberto // Adiciona o tempo aberto ao array
                ];
            })->toArray();
        }

        return null;
    }

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
            $chamadosCollection = $chamadosCollection->filter(function ($chamado) use ($request) {
                return strtotime($chamado['tempoAberto']) >= now()->subDays($request->periodo)->timestamp;
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

        // 🔹 Paginação manual (50 por página)
        $chamadosPaginados = $chamadosCollection->forPage($request->input('page', 1), 100);

        // 🔹 Contagem de chamados por status e nível
        $statusCount = [
            'Em Aberto' => $chamadosCollection->where('status', 'Em atendimento')->count(),
            'Aguardando' => $chamadosCollection->where('status', 'Aguardando')->count(),

            'Em Aberto Junior' => $chamadosCollection->where('status', 'Em atendimento')->where('nivel', 'Junior')->count(),
            'Aguardando Junior' => $chamadosCollection->where('status', 'Aguardando')->where('nivel', 'Junior')->count(),

            'Em Aberto Pleno' => $chamadosCollection->where('status', 'Em atendimento')->where('nivel', 'Pleno')->count(),
            'Aguardando Pleno' => $chamadosCollection->where('status', 'Aguardando')->where('nivel', 'Pleno')->count(),

            'Em Aberto Senior' => $chamadosCollection->where('status', 'Em atendimento')->where('nivel', 'Senior')->count(),
            'Aguardando Senior' => $chamadosCollection->where('status', 'Aguardando')->where('nivel', 'Senior')->count(),
        ];


        

        return view('dashboard', compact('chamadosPaginados', 'statusCount', 'chamadosCollection'));
    }

   

    private function getNivel($ownerTeam)
    {
        if (strpos($ownerTeam, 'Suporte - Técnico Junior') !== false) {
            return 'Junior';
        } elseif (strpos($ownerTeam, 'Suporte - Técnico Pleno') !== false) {
            return 'Pleno';
        } elseif (strpos($ownerTeam, 'Suporte - Técnico Sênior ') !== false) {
            return 'Senior';
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