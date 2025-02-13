<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filtrarChamadosNaoResolvidos(Ticket::query());

        // Aplicação de filtros
        if ($request->filled('numero_chamado')) {
            $query->where('protocolo', 'LIKE', '%' . $request->numero_chamado . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('periodo')) {
            $query->where('abertoEm', '>=', now()->subDays($request->periodo));
        }

        if ($request->filled('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        if ($request->ordenacao == 'recente') {
            $query->orderBy('abertoEm', 'desc');
        } elseif ($request->ordenacao == 'antigo') {
            $query->orderBy('abertoEm', 'asc');
        }

        // Paginação dos chamados
        $chamados = $query->paginate(10);
        $chamadosCollection = collect($chamados->items());

        // Contagem de chamados por status
        $statusCount = [
            'Em Aberto' => $chamadosCollection->where('status', 'Em Aberto')->count(),
            'Aguardando' => $chamadosCollection->where('status', 'Aguardando')->count(),
        ];

        // Cálculo do tempo de atendimento e escalonamento
        foreach ($chamadosCollection as $ticket) {
            $tempoAtendimento = Carbon::parse($ticket->abertoEm)->diffInMinutes(now());
            $ticket->tempoAtendimento = $tempoAtendimento;

            if ($ticket->nivel === 'Junior' && $tempoAtendimento > 30) {
                $ticket->escalonamento = "Escalonar para Pleno";
            } elseif ($ticket->nivel === 'Pleno' && $tempoAtendimento > 150) {
                $ticket->escalonamento = "Escalonar para Sênior";
            } else {
                $ticket->escalonamento = null;
            }
        }

        return view('dashboard', compact('chamados', 'statusCount', 'chamadosCollection'));
    }

    /**
     * Filtra chamados que não estão resolvidos.
     */
    private function filtrarChamadosNaoResolvidos($query)
    {
        return $query->whereNotIn('status', ['Fechado', 'Resolvido']);
    }
}
