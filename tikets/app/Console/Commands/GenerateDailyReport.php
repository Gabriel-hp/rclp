<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket; // Substitua pelo seu modelo
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class GenerateDailyReport extends Command
{
    protected $signature = 'report:generate';
    protected $description = 'Gera um relatório diário em PDF e salva no storage';

    public function handle()
    {
        // Obtém os dados dos chamados agrupados por nível
        $chamados = Ticket::orderBy('nivel')->get()->groupBy('nivel');

        // Gera o PDF
        $pdf = Pdf::loadView('reports.daily', compact('chamados'));

        // Define o nome do arquivo
        $fileName = 'relatorio_diario_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';

        // Salva o PDF no storage
        Storage::disk('local')->put('reports/' . $fileName, $pdf->output());

        $this->info('Relatório gerado e salvo com sucesso: ' . $fileName);
    }
}