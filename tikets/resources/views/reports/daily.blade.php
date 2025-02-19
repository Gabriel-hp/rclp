<?php 
use Carbon\Carbon;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Relatório Diário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .totais {
            margin-bottom: 20px;
        }
        .totais h3 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Relatório Diário - {{ now()->format('d/m/Y') }}</h1>

    <!-- Exibe o total de chamados -->
    <div class="totais">
        <h3>Total de Chamados: {{ $totalChamados }}</h3>
    </div>

    <!-- Exibe o total por níveis -->
    <div class="totais">
        <h3>Total por Níveis:</h3>
        <ul>
            @foreach ($totalPorNivel as $nivel => $total)
                <li>{{ $nivel }}: {{ $total }}</li>
            @endforeach
        </ul>
    </div>

    <!-- Exibe os chamados agrupados por nível -->
    @foreach ($chamados as $nivel => $grupo)
        <h2>Grupo: {{ $nivel }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Protocolo</th>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th>Tempo</th>
                    <th>Escalonamento</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grupo as $chamado)
                    @php
                        $tempoAberto = isset($chamado['tempoAberto']) ? Carbon::parse($chamado['tempoAberto']) : null;
                        $diferenca = $tempoAberto ? $tempoAberto->diff(now()) : null;
                        $horas = $diferenca ? $diferenca->h + ($diferenca->days * 24) : 0;
                        $minutos = $diferenca ? $diferenca->i : 0;
                        $alerta = '';

                        if ($chamado['status'] === 'Em atendimento' && $tempoAberto) {
                            if ($chamado['nivel'] === 'Junior' && $tempoAberto->diffInMinutes(now()) >= 30) {
                                $alerta = 'Escalonar para Pleno';
                            } elseif ($chamado['nivel'] === 'Pleno' && $tempoAberto->diffInMinutes(now()) >= 180) {
                                $alerta = 'Escalonar para Sênior';
                            }
                        } 
                        if ($chamado['status'] === 'Aguardando' && $tempoAberto) {
                            if ($chamado['nivel'] === 'Junior' && $tempoAberto->diffInMinutes(now()) >= 360) {
                                $alerta = 'Escalonar para Pleno';
                            } elseif ($chamado['nivel'] === 'Pleno' && $tempoAberto->diffInMinutes(now()) >= 480) {
                                $alerta = 'Escalonar para Sênior';
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $chamado['protocolo'] }}</td>
                        <td>{{ $chamado['cliente'] }}</td>
                        <td>{{ $chamado['status'] }}</td>
                        <td>
                            @if($tempoAberto)
                                {{ $horas }} horas e {{ $minutos }} minutos
                            @else
                                Não informado
                            @endif
                        </td>
                        <td>{{ $alerta }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</body>
</html>