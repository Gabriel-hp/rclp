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
                    <th>Usuario</th>
                    <th>Assunto</th>
                    <th>Protocolo</th>
                    <th>Status</th>
                    <th>Aberto Em</th>
                    <th>Nível</th>
                    <th>Tempo Aberto</th>
                    <th>Escalonamento</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chamados as $chamado)
                    <tr>
                        <td>{{ $chamado->criadoPor }}</td>
                        <td>{{ $chamado->assunto }}</td>
                        <td>{{ $chamado->protocolo }}</td>
                        <td>
                            <span class="badge bg-{{ $chamado->status == 'Em Aberto' ? 'warning' : ($chamado->status == 'Aguardando' ? 'info' : 'success') }}">
                                {{ $chamado->status }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($chamado['abertoEm'])->format('d/m/Y H:i') }}</td>
                        <td>{{ $chamado->nivel }}</td>
                        <td>
                            @php
                            $abertoEm = \Carbon\Carbon::parse($chamado->abertoEm); // Corrigido para acessar como objeto
                            $agora = \Carbon\Carbon::now(); // Hora atual
                            $diferenca = $abertoEm->diff($agora);

                            $tempoAberto = sprintf('%d dias, %d horas e %d minutos', $diferenca->d, $diferenca->h, $diferenca->i);

                            @endphp
                            {{ $tempoAberto }}
                        </td>
                        <td>
                            @php
                            
                            $tempoMinutos = $diferenca->days * 1440 + $diferenca->h * 60 + $diferenca->i;
                            $limite = null;

                            if ($chamado->status == 'Aguardando') {
                                $limite = 'Aguardando';
                            } elseif ($chamado->nivel == 'Junior') {
                                $limite = 30; // 30 minutos
                            } elseif ($chamado->nivel == 'Pleno') {
                                $limite = 60; // 60 minutos
                            }

                            // Verificação para "Aguardando" maior que 5 dias (7200 minutos)
                            $acionarComercial = ($chamado->status == 'Aguardando' && $tempoMinutos > 7200);
                        @endphp

                        @if($acionarComercial)
                            <span class="text-warning">Acionar Comercial</span>
                        @elseif($limite && $tempoMinutos > $limite)
                            <span class="text-danger">Necessário Escalonamento</span>
                        @else
                            <span class="text-success">Dentro do Tempo</span>
                        @endif

                        </td>
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
        type: 'doughnut',
        data: {
            labels: ['Em Aberto', 'Aguardando'],
            datasets: [{
                label: 'Número de Chamados',
                data: [{{ $statusCount['Em Aberto'] ?? 0 }}, {{ $statusCount['Aguardando'] ?? 0 }}],
                backgroundColor: ['#6A31D8', '#0DCAF0']
            }]
        },
    });
</script>
@endsection
