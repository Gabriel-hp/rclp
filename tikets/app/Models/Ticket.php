<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocolo', 'assunto', 'abertoEm', 'criadoPor', 
        'resolvidoEm', 'fechadoEm', 'status', 'origem', 'nivel'
    ];

    protected $dates = ['abertoEm', 'resolvidoEm', 'fechadoEm'];

    public function scopeFiltrarChamadosNaoResolvidos($query)
    {
        return $query->whereNotIn('status', ['Fechado', 'Resolvido']);
    }
}
