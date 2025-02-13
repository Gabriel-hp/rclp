@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Painel de Chamados</h2>

    <!-- Filtros -->
    <form method="GET" action="{{ route('dashboard') }}" class="mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="numero_chamado" class="form-control" placeholder="Número do chamado" value="{{ request('numero_chamado') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">Todos os status</option>
                    <option value="Em Aberto" {{ request('status') == 'Em Aberto' ? 'selected' : '' }}>Em Aberto</option>
                    <option value="Aguardando" {{ request('status') == 'Aguardando' ? 'selected' : '' }}>Aguardando</option>
                    <option value="Fechado" {{ request('status') == 'Fechado' ? 'selected' : '' }}>Fechado</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="periodo" class="form-control">
                    <option value="">Período</option>
                    <option value="7" {{ request('periodo') == '7' ? 'selected' : '' }}>Últimos 7 dias</option>
                    <option value="30" {{ request('periodo') == '30' ? 'selected' : '' }}>Últimos 30 dias</option>
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

    <div class="row text-center align-items-center">
    <div class="col-md-6 d-flex justify-content-center">
        <div class="d-flex gap-3">
            <div class="card-aber card text-white small-card">
                <div class="card-body">
                    <h5 class="card-title">Em Aberto</h5>
                    <p class="card-text fs-4">{{ $statusCount['Em Aberto'] ?? 0 }}</p>
                </div>
            </div>
            <div class="card bg-info text-white small-card">
                <div class="card-body">
                    <h5 class="card-title">Aguardando</h5>
                    <p class="card-text fs-4">{{ $statusCount['Aguardando'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 d-flex justify-content-center">
        <canvas class="grafiChar" id="chamadosChart"></canvas>
    </div>
</div>


    <!-- Lista de Chamados -->
    <div class="table-responsive mt-4">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Protocolo</th>
                    <th>Status</th>
                    <th>Aberto Em</th>
                    <th>Tempo Aberto</th>
                    <th>Escalonamento</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chamados as $chamado)
                    @php
                        $tempoAtendimento = now()->diffInMinutes($chamado->abertoEm);
                        $alerta = '';
                        if ($chamado->status === 'Em Atendimento') {
                            if ($chamado->nivel === 'Júnior' && $tempoAtendimento >= 30) {
                                $alerta = 'Escalonar para Pleno';
                            } elseif ($chamado->nivel === 'Pleno' && $tempoAtendimento >= 150) {
                                $alerta = 'Escalonar para Sênior';
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $chamado->id }}</td>
                        <td>{{ $chamado->protocolo }}</td>
                        <td>
                            <span class="badge bg-{{ $chamado->status == 'Em Aberto' ? 'warning' : ($chamado->status == 'Aguardando' ? 'info' : 'success') }}">
                                {{ $chamado->status }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($chamado['abertoEm'])->format('d/m/Y H:i') }}</td></td>
                        <td class="text-red-500 font-bold">{{ $alerta }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Paginação -->
    <div class="mt-4">
        {{ $chamados->links() }}
    </div>
</div>

<script>
    var ctx = document.getElementById('chamadosChart').getContext('2d');
    var chamadosChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Em Aberto', 'Aguardando'],
            datasets: [{
                label: 'Número de Chamados',
                data: [{{ $statusCount['Em Aberto'] ?? 0 }}, {{ $statusCount['Aguardando'] ?? 0 }}],
                backgroundColor: ['#0DCAF0', '#6A31D8']
            }]
        },
    });
</script>
@endsection