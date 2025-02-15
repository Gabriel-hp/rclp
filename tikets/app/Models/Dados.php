<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dados extends Model
{
    use HasFactory;

    protected $connection = 'dados'; // Define o banco externo
    protected $table = 'tickets'; // Nome da tabela no banco externo

    protected $fillable = ['protocolo', 'status', 'abertoEm', 'nivel', 'descricao'];

}