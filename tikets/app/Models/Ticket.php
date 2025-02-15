<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocolo', 'assunto', 'abertoEm', 'criadoPor',
        'resolvidoEm', 'fechadoEm', 'status', 'origem', 'nivel'
    ];

    protected $dates = ['abertoEm', 'resolvidoEm', 'fechadoEm'];

    /**
     * Filtra chamados que não estão resolvidos ou fechados.
     */
    public function scopeFiltrarChamadosNaoResolvidos($query)
    {
        return $query->whereNotIn('status', ['Fechado', 'Resolvido']);
    }

    /**
     * Consome a API do Movidesk e armazena os tickets no banco de dados.
     */

    public static function importarTickets()
    {
        $url = "https://api.movidesk.com/public/v1/tickets";
        $token = "8ef83eef-7ee0-4629-8d6d-ed0680eee679";
        $params = [
            'token' => $token,
            '$select' => 'id,type,origin,status'
        ];

        $response = Http::get($url, $params);

        if ($response->successful()) {
            $tickets = $response->json();

            foreach ($tickets as $ticket) {
                self::updateOrCreate(
                    ['protocolo' => $ticket['id']], // Usa o protocolo (ID) como identificador único
                    [
                        'assunto' => $ticket['type'] ?? 'Sem Assunto',
                        'abertoEm' => now(),
                        'criadoPor' => 'API',
                        'resolvidoEm' => null,
                        'fechadoEm' => null,
                        'status' => $ticket['status'] ?? 'Desconhecido',
                        'origem' => $ticket['origin'] ?? 'Indefinido',
                        'nivel' => 'Normal'
                    ]
                );
            }
        } else {
            throw new \Exception('Erro ao consumir a API Movidesk');
        }
    }
}
