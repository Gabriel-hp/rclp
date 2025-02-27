<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Ticket;
use Carbon\Carbon;

class AtualizarTickets extends Command
{
    protected $signature = 'tickets:atualizar';
    protected $description = 'Busca os chamados da API e atualiza o banco de dados';

    public function handle()
    {
        $apiUrl = "https://api.movidesk.com/public/v1/tickets";
        $apiToken = "8ef83eef-7ee0-4629-8d6d-ed0680eee679";

        $params = [
            'token' => $apiToken,
            '$select' => 'protocol,status,ownerTeam,createdDate,lastUpdate, resolvedIn',
            '$filter' => "status eq 'Em atendimento' or status eq 'Aguardando' or status eq 'Data Center'",
            '$expand' => 'clients($select=businessName)', 
        ];

        $response = Http::get($apiUrl, $params);

        if (!$response->successful()) {
            $this->error('Erro ao   buscar tickets da API');
            return;
        }

        $tickets = collect($response->json())->map(function ($ticket) {
            $createdDate = $ticket['createdDate'] ?? null;
            $abertoEm = $createdDate ? Carbon::parse(preg_replace('/\..+/', '', $createdDate), 'UTC')->setTimezone('America/Manaus') : null;
            $tempoAberto = $abertoEm ? $abertoEm->diffInSeconds(Carbon::now()) : null;
            $cliente = !empty($ticket['clients']) && is_array($ticket['clients']) ? $ticket['clients'][0]['businessName'] ?? 'Cliente' : 'Cliente';
            $lastUpdate = $ticket['lastUpdate'] ?? null;
            $lastUpdate = $lastUpdate ? Carbon::parse(preg_replace('/\..+/', '', $lastUpdate), 'UTC')->setTimezone('America/Manaus') : null;
            $resolvedIn = $ticket['resolvedIn'] ?? null;

            return [
                'protocolo' => $ticket['protocol'] ?? 'N/A',
                'cliente' => $cliente,
                'status' => $ticket['status'] ?? 'Desconhecido',
                'nivel' => $this->getNivel($ticket['ownerTeam'] ?? 'Indefinido'),
                'aberto_em' => $abertoEm,
                'tempo_aberto' => $tempoAberto,
                'lastUpdate' => $lastUpdate,
                'resolvedIn' => $resolvedIn,
            ];
        });

        foreach ($tickets as $dadosTicket) {
            Ticket::updateOrCreate(
                ['protocolo' => $dadosTicket['protocolo']],
                $dadosTicket
            );
        }

        $this->info('Tickets atualizados com sucesso.');
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
