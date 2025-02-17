@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <div class="row align-items-center">
        <div class="col-md-6 d-flex justify-content-center">
            <div class="tot">
            <div class="totall">
                    <div class="card-totall bg-tot1 card text-white ">
                        <div class="card-body">
                            <h5 class="card-title">Em Aberto</h5>
                            <p class="card-text fs-4">{{ $statusCount['Em Aberto'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="card-totall infre card bg-tot text-white ">
                        <div class="card-body">
                            <h5 class="card-title">Aguardando</h5>
                            <p class="card-text ">{{ $statusCount['Aguardando'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="container-cards">
                <div class="sup">
                    <div class="card-aber card text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Em Aberto N1</h5>
                            <p class="card-text fs-4">{{ $statusCount['Em Aberto Junior'] ?? 0 }}</p>

                        </div>
                    </div>
                    <div class="card bg-agur text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Aguardando N1</h5>
                            <p class="card-text fs-4">{{ $statusCount['Aguardando Junior'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="infe ">
                <div class="card-aber card text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Em Aberto N2</h5>
                            <p class="card-text fs-4">{{ $statusCount['Em Aberto Pleno'] ?? 0 }}</p>

                        </div>
                    </div>
                    <div class="card bg-agur text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Aguardando N2</h5>
                            <p class="card-text fs-4">{{ $statusCount['Aguardando Pleno'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="infe ">
                <div class="card-aber card text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Em Aberto N3</h5>
                            <p class="card-text fs-4">{{ $statusCount['Em Aberto Senior'] ?? 0 }}</p>

                        </div>
                    </div>
                    <div class="card bg-agur text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Aguardando N3</h5>
                            <p class="card-text fs-4">{{ $statusCount['Aguardando Senior'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="col-md-6 d-flex justify-content-center">
            <canvas class="grafiChar" id="chamadosChart"></canvas>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" action="{{ route('dashboard') }}" class="mb-4 mt-5">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="numero_chamado" class="form-control" placeholder="Número do Protocolo" value="{{ request('numero_chamado') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">Todos os status</option>
                    <option value="Em atendimento" {{ request('status') == 'Em atendimento' ? 'selected' : '' }}>Em atendimento</option>
                    <option value="Aguardando" {{ request('status') == 'Aguardando' ? 'selected' : '' }}>Aguardando</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="periodo" class="form-control">
                    <option value="">Período</option>
                    <option value="7" {{ request('periodo') == '7' ? 'selected' : '' }}>Últimos 7 dias</option>
                    <option value="30" {{ request('periodo') == '30' ? 'selected' : '' }}>Últimos 30 dias</option>
                </select>
            </div>
            
            <div class="col-md-1">
                <select name="nivel" class="form-control">
                    <option value="">Grupo</option>
                    <option value="Junior" {{ request('nivel') == 'Junior' ? 'selected' : '' }}>Junior</option>
                    <option value="Pleno" {{ request('nivel') == 'Pleno' ? 'selected' : '' }}>Pleno</option>
                    <option value="Senior" {{ request('nivel') == 'Sênior' ? 'selected' : '' }}>Sênior</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="ordenacao" class="form-control">
                    <option value="recente" {{ request('ordenacao') == 'recente' ? 'selected' : '' }}>Mais antigos</option>
                    <option value="antigo" {{ request('ordenacao') == 'antigo' ? 'selected' : '' }}>Mais Recentes</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Lista de Chamados -->
    <div class="table-responsive mt-4">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Cliente</th>
                    <th>Protocolo</th>
                    <th>Status</th>
                    <th>Grupo</th>
                    <th>Tempo</th>
                    <th>Escalonamento</th>
                    <th>Ticket</th>
                </tr>
            </thead>
            <tbody>
            @foreach($chamadosPaginados as $chamado)
            @php
            
              // Converter tempoAberto para um objeto Carbon, garantindo que não seja nulo
                $tempoAberto = isset($chamado['tempoAberto']) ? \Carbon\Carbon::parse($chamado['tempoAberto']) : null;
                $tempoAtendimento = $tempoAberto ? $tempoAberto->diffInMinutes(now()) : 0;
                $alerta = '';

            // Verifica se o chamado está "Em Atendimento" e se precisa ser escalonado
            if ($chamado['status'] === 'Em atendimento' && $tempoAberto) {
                if ($chamado['nivel'] === 'Junior' && $tempoAtendimento >= 30) {
                    $alerta = 'Escalonar para Pleno';
                } elseif ($chamado['nivel'] === 'Pleno' && $tempoAtendimento >= 180) {
                    $alerta = 'Escalonar para Sênior';
                }
            } 
            if ($chamado['status'] === 'Aguardando' && $tempoAberto) {
                if ($chamado['nivel'] === 'Junior' && $tempoAtendimento >= 360) {
                    $alerta = 'Escalonar para Pleno';
                } elseif ($chamado['nivel'] === 'Pleno' && $tempoAtendimento >= 480) {
                    $alerta = 'Escalonar para Sênior';
                }
            } 
            @endphp

    <tr>
        <td class="clientes">{{ $chamado['cliente'] }}</td> <!-- Exibe o nome do cliente -->
        <td class="clientes-b">{{ $chamado['protocolo'] }}</td>
        <td class="clientes-b">
            <span class="badge bg-{{ $chamado['status'] == 'Em atendimento' ? 'success' : ($chamado['status'] == 'Aguardando' ? 'info' : 'warning') }}">
                {{ $chamado['status'] }}
            </span>
        </td>
        <td class="clientes-b">{{ $chamado['nivel'] }}</td>
        <td class="clientes-b">
            @if($tempoAberto)
                {{ $tempoAberto->diff(now())->format('%H horas e %I minutos') }}
            @else
                Não informado
            @endif
        </td>
        <td class="alert-esc clientes-b">{{ $alerta }}</td>
        <td class="clientes-b">
            <a href="https://logicpro.movidesk.com/Ticket/EditByProtocol/{{ $chamado['protocolo'] }}" target="_blank">
                <button type="button" class="btn btn-light">Acesse o ticket</button>
            </a>
        </td>
    </tr>
@endforeach



        </tbody>
        </table>
    </div>
    
    <!-- Paginação -->

</div>

<script>
    var ctx = document.getElementById('chamadosChart').getContext('2d');
    var chamadosChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Em Aberto', 'Aguardando'],
            datasets: [{
                label: 'Número de Chamados',
                data: [{{ $statusCount['Em Aberto'] ?? 0 }}, {{ $statusCount['Aguardando'] ?? 0 }}],
                backgroundColor: ['#D77534', '#1D7287']
            }]
        },
    });
    
</script>
@endsection
