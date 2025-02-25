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
            font-size: 15px;
        }
        h2{
            font-size: 15px;
            text-align: center;
            background-color:rgb(27, 85, 0);
            color: #f2f2f2;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            
        }
        th, td {
            border: 1px solid #000;
            padding: 2px;
            text-align: left;
            font-size: 10px;
        }
        .tab-form{
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
        }
        .totais {
            margin-bottom: 10px;
        }
        .totais h3 {
            margin-bottom: 10px;
        }
        .totais {
        text-align: center;
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 20px;
        }


        .status-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            width: 130px;
            text-align: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 280px; 
        }

        .status-container-cham{
            display: flex;
            flex-direction: row;
        }

        


        .status-card h4 {
            margin-bottom: 10px;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .status-card p {
            font-size: 1.1rem;
            margin: 5px 0;
        }

        .status-card span {
            font-weight: bold;
        }

        /* Cores para diferenciar cada nível */
        .junior {
            background: #d1ecf1;
            border-left: 5px solid #17a2b8;
        }

        .pleno {
            background: #d4edda;
            border-left: 5px solid #28a745;
        }

        .senior {
            background: #f8d7da;
            border-left: 5px solid #dc3545;
        }

    </style>
    </head>
<body>

    <h1>Relatório Diário - {{ now()->format('d/m/Y H:i') }}</h1>

    <div class="totais">
        <h3>Total de Chamados: {{ $totalChamados }}</h3>
    </div>
        <div class="status-container-cham ">
                <div class="status-card">
                    <h4>Geral</h4>
                    <p class="card-text">Em atendimento: <span>{{ $statusCount['Em atendimento'] ?? 0 }}</span></p>
                    <p class="card-text">Aguardando: <span>{{ $statusCount['Aguardando'] ?? 0 }}</span></p>
                </div>
        </div>
        


    <!-- Exibe os chamados agrupados por nível -->
    @foreach ($chamados as $nivel => $grupo)
        <h2>{{ $nivel }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Protocolo</th>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th>Tempo</th>
                    <th>Escalonamento</th>
                    <th>Ultima ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grupo as $chamado)
                    @php
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
                        if ($chamado->nivel === 'Junior' && $tempoAbertoEmMinutos >= 30) {
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

                    @endphp
                    <tr class="tab-form">
                        <td>{{ $chamado['protocolo'] }}</td>
                        <td>{{ $chamado['cliente'] }}</td>
                        <td>{{ $chamado['status'] }}</td>
                        <td>{{ $chamado->tempo_aberto_formatado }}</td>
                        <td>{{ $chamado->escalonamento }}</td>
                        <td>{{ \Carbon\Carbon::parse($chamado->lastUpdate)->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    
</body>
</html>