<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function generateDailyReport()
    {
        // Busca os chamados diretamente do banco de dados
        $tickets = Ticket::all();

        if ($tickets->isEmpty()) {
            return redirect()->back()->with('erro', 'Nenhum chamado encontrado.');
        }

        // Converte os tickets em uma coleção
        $chamadosCollection = collect($tickets);

        // Agrupa os chamados por nível
        $chamadosAgrupados = $chamadosCollection->groupBy('nivel');

        // Calcula o total de chamados
        $totalChamados = $chamadosCollection->count();

        // Calcula o total por níveis
        $totalPorNivel = $chamadosCollection->groupBy('nivel')->map->count();

        // Calcula os totais por status e nível
        $statusCount = [
            'Em atendimento' => Ticket::where('status', 'Em atendimento')->count(),
            'Aguardando' => Ticket::where('status', 'Aguardando')->count(),
            'Concluído' => Ticket::where('status', 'Concluído')->count(),
        ];

        // Gera o PDF
        $pdf = \PDF::loadView('reports.daily', [
            'chamados' => $chamadosAgrupados,
            'totalChamados' => $totalChamados,
            'totalPorNivel' => $totalPorNivel,
            'statusCount' => $statusCount,
        ])->setPaper('a4', 'landscape');
        ;

        // Retorna o PDF para download
        return $pdf->download('relatorio_diario.pdf');
    }

    public function index(Request $request)
    {
        $numeroChamado = $request->input('numero_chamado');
        $status = $request->input('status');
        $periodo = $request->input('periodo');
        $nivel = $request->input('nivel');
        $ordenacao = $request->input('ordenacao', 'recente');

        // Construção da query com filtros
        $query = Ticket::query();

        if ($numeroChamado) {
            $query->where('protocolo', $numeroChamado);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($nivel) {
            $query->where('nivel', $nivel);
        }

        if ($periodo) {
            $query->where('aberto_em', '>=', Carbon::now()->subDays($periodo));
        }

        $query->orderBy('aberto_em', $ordenacao === 'recente' ? 'desc' : 'asc');

        // Obter os chamados filtrados
        $chamados = $query->get();

        // Adiciona formatação do tempo e lógica de escalonamento
        $chamados->transform(function ($chamado) {
            if ($chamado->tempo_aberto) {
                // Converter segundos para horas e minutos
                $totalHoras = floor($chamado->tempo_aberto / 3600); // Total de horas inteiras
                $minutosRestantes = floor(($chamado->tempo_aberto % 3600) / 60); // Minutos restantes

                // Formatar para exibir "X horas e Y minutos"
                $chamado->tempo_aberto_formatado = sprintf('%d horas e %d minutos', $totalHoras, $minutosRestantes);

                // Converter tempo de segundos para minutos para a lógica de escalonamento
                $tempoAbertoEmMinutos = floor($chamado->tempo_aberto / 60);

                // Lógica de escalonamento
                if ($chamado->status === 'Em atendimento') {
                    if ($chamado->nivel === 'Junior' && $tempoAbertoEmMinutos >= 3) {
                        $chamado->escalonamento = 'Escalonar para Pleno';
                    } elseif ($chamado->nivel === 'Pleno' && $tempoAbertoEmMinutos >= 180) {
                        $chamado->escalonamento = 'Escalonar para Sênior';
                    } else {
                        $chamado->escalonamento = 'Sem ação';
                    }
                } elseif ($chamado->status === 'Aguardando') {
                    if ($chamado->nivel === 'Junior' && $tempoAbertoEmMinutos >= 360) {
                        $chamado->escalonamento = 'Escalonar para Pleno';
                    } elseif ($chamado->nivel === 'Pleno' && $tempoAbertoEmMinutos >= 480) {
                        $chamado->escalonamento = 'Escalonar para Sênior';
                    } else {
                        $chamado->escalonamento = 'Sem ação';
                    }
                } else {
                    $chamado->escalonamento = 'Sem ação';
                }
            } else {
                $chamado->tempo_aberto_formatado = 'N/A';
                $chamado->escalonamento = 'Sem ação';
            }

            return $chamado;
        });







        // Contagem de chamados por status e nível
        $statusCount = [
            'Em atendimento' => Ticket::where('status', 'Em atendimento')->count(),
            'Aguardando' => Ticket::where('status', 'Aguardando')->count(),
            'Em atendimento Junior' => Ticket::where('status', 'Em atendimento')->where('nivel', 'Junior')->count(),
            'Aguardando Junior' => Ticket::where('status', 'Aguardando')->where('nivel', 'Junior')->count(),
            'Em atendimento Pleno' => Ticket::where('status', 'Em atendimento')->where('nivel', 'Pleno')->count(),
            'Aguardando Pleno' => Ticket::where('status', 'Aguardando')->where('nivel', 'Pleno')->count(),
            'Em atendimento Senior' => Ticket::where('status', 'Em atendimento')->where('nivel', 'Senior')->count(),
            'Aguardando Senior' => Ticket::where('status', 'Aguardando')->where('nivel', 'Senior')->count(),
        ];

        // Retornar os dados para a view
        return view('dashboard', [
            'chamadosCollection' => $chamados,
            'statusCount' => $statusCount
        ]);
    }
}
