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
                            <h5 class="card-title">Em Aberto Junior</h5>
                            <p class="card-text fs-4">{{ $statusCount['Em Aberto Junior'] ?? 0 }}</p>

                        </div>
                    </div>
                    <div class="card bg-agur text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Aguardando Junior</h5>
                            <p class="card-text fs-4">{{ $statusCount['Aguardando Junior'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="infe ">
                <div class="card-aber card text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Em Aberto Pleno</h5>
                            <p class="card-text fs-4">{{ $statusCount['Em Aberto Pleno'] ?? 0 }}</p>

                        </div>
                    </div>
                    <div class="card bg-agur text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Aguardando Pleno</h5>
                            <p class="card-text fs-4">{{ $statusCount['Aguardando Pleno'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="infe ">
                <div class="card-aber card text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Em Aberto Senior</h5>
                            <p class="card-text fs-4">{{ $statusCount['Em Aberto Senior'] ?? 0 }}</p>

                        </div>
                    </div>
                    <div class="card bg-agur text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Aguardando Senior</h5>
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
                    <option value="">Nível</option>
                    <option value="Junior" {{ request('nivel') == 'Junior' ? 'selected' : '' }}>Junior</option>
                    <option value="Pleno" {{ request('nivel') == 'Pleno' ? 'selected' : '' }}>Pleno</option>
                    <option value="Senior" {{ request('nivel') == 'Sênior' ? 'selected' : '' }}>Sênior</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="ordenacao" class="form-control">
                    <option value="recente" {{ request('ordenacao') == 'recente' ? 'selected' : '' }}>Mais recentes</option>
                    <option value="antigo" {{ request('ordenacao') == 'antigo' ? 'selected' : '' }}>Mais antigos</option>
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
                </tr>
            </thead>
            <tbody>
            @foreach($chamadosPaginados as $chamado)
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
                @if($chamado['tempoAberto'])
                    {{ $chamado['tempoAberto']->h }} horas e {{ $chamado['tempoAberto']->i }} minutos
                @else
                    Não informado
                @endif
                </td>
                   
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
