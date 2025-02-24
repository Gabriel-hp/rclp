
@extends('layouts.app')

@section('content')

<div class="container mt-4">
    <div class="row align-items-center">
        <div class="col-md-6 d-flex justify-content-center">
            <div class="tot">
                <div class="totall">
                    <div class="card-totall bg-tot1 card text-white">
                        <div class="card-body">
                            <h5 class="card-title">Em Aberto</h5>
                            <p class="card-text fs-4">{{ $statusCount['Em atendimento'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="card-totall infre card bg-tot text-white">
                        <div class="card-body">
                            <h5 class="card-title">Aguardando</h5>
                            <p class="card-text">{{ $statusCount['Aguardando'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-cards">
                <div class="sup">
                    <div class="card-aber card text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Em Aberto N1</h5>
                            <p class="card-text fs-4">{{ $statusCount['Em atendimento Junior'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="card bg-agur text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Aguardando N1</h5>
                            <p class="card-text fs-4">{{ $statusCount['Aguardando Junior'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="infe">
                    <div class="card-aber card text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Em Aberto N2</h5>
                            <p class="card-text fs-4">{{ $statusCount['Em atendimento Pleno'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="card bg-agur text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Aguardando N2</h5>
                            <p class="card-text fs-4">{{ $statusCount['Aguardando Pleno'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="infe">
                    <div class="card-aber card text-white small-card">
                        <div class="card-body">
                            <h5 class="card-title">Em Aberto N3</h5>
                            <p class="card-text fs-4">{{ $statusCount['Em atendimento Senior'] ?? 0 }}</p>
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
     <!-- Botão para gerar o relatório -->
     <div class="text-center mb-4">
        <a href="{{ route('gerar.relatorio') }}" class="btn btn-primary">
            Gerar Relatório Diário
        </a>
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
                    <option value="recente" {{ request('ordenacao') == 'recente' ? 'selected' : '' }}>Mais Recentes</option>
                    <option value="antigo" {{ request('ordenacao') == 'antigo' ? 'selected' : '' }}>Mais antigos</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
            </div>
        </div>
    </form>
    <!-- Botão para gerar o relatório -->
    <div class="container mt-4">
   

    <!-- Lista de Chamados -->
    <div class="table-responsive mt-4">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Protocolo</th>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th>Grupo</th>
                    <th>Tempo</th>
                    <th>Ação</th>
                    <th>Ticket</th>
                </tr>
            </thead>
            <tbody id="tabelaChamados">
            @foreach ($chamadosCollection as $chamado)
    <tr>
        <td>{{ $chamado->protocolo }}</td>
        <td>{{ $chamado->cliente }}</td>
        <td>
            <span class="badge bg-{{ $chamado->status === 'Em atendimento' ? 'success' : ($chamado->status === 'Aguardando' ? 'info' : 'warning') }}">
                {{ $chamado->status }}
            </span>
        </td>
        <td>{{ $chamado->nivel }}</td>
        <td>{{ $chamado->tempo_aberto_formatado }}</td>
        <td>{{ $chamado->escalonamento }}</td>
        <td>
            <a href="https://logicpro.movidesk.com/Ticket/EditByProtocol/{{ $chamado->protocolo }}" target="_blank">
                <button type="button" class="btn btn-light">Acesse o ticket</button>
            </a>
        </td>
    </tr>
@endforeach

        </tbody>

        </table>
    </div>
</div>



<script>
    var ctx = document.getElementById('chamadosChart').getContext('2d');
    var chamadosChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Em atendimento', 'Aguardando'],
            datasets: [{
                label: 'Número de Chamados',
                data: [{{ $statusCount['Em atendimento'] ?? 0 }}, {{ $statusCount['Aguardando'] ?? 0 }}],
                backgroundColor: ['#D77534', '#1D7287']
            }]
        },
    });

    function atualizarChamados() {
    fetch('/dashboard/update')
        .then(response => response.json())
        .then(data => {
            let tabela = document.getElementById('tabelaChamados');
            tabela.innerHTML = "";

            data.forEach(ticket => {
                let newRow = tabela.insertRow();
                let cells = [
                    ticket.protocolo,
                    ticket.cliente,
                    `<span class="badge bg-${ticket.status === 'Em atendimento' ? 'success' : (ticket.status === 'Aguardando' ? 'info' : 'warning')}">${ticket.status}</span>`,
                    ticket.nivel,
                    ticket.aberto_em,
                    `<a href="https://logicpro.movidesk.com/Ticket/EditByProtocol/${ticket.protocolo}" target="_blank"><button type="button" class="btn btn-light">Acesse o ticket</button></a>`
                ];
                
                cells.forEach((content, index) => {
                    let cell = newRow.insertCell(index);
                    cell.innerHTML = content;
                });
            });
        })
        .catch(error => console.error('Erro ao atualizar:', error));
}

setInterval(atualizarChamados, 30000);


document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('toggle-theme');
    const body = document.body;
    const icon = toggleButton.querySelector('.icon');

    // Verifica o tema salvo no localStorage e aplica ao carregar a página
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light-mode') {
        body.classList.add('light-mode');
        toggleButton.classList.add('light');
        icon.textContent = "☀️"; // Define o ícone para sol se estiver no tema claro
    } else {
        icon.textContent = "🌙"; // Define o ícone para lua se estiver no tema escuro
    }

    // Alterna entre os temas ao clicar no botão
    toggleButton.addEventListener('click', function () {
        body.classList.toggle('light-mode');
        toggleButton.classList.toggle('light');

        if (body.classList.contains('light-mode')) {
            localStorage.setItem('theme', 'light-mode');
            icon.textContent = "☀️"; // Altera para ícone de sol no tema claro
        } else {
            localStorage.setItem('theme', 'dark-mode');
            icon.textContent = "🌙"; // Altera para ícone de lua no tema escuro
        }
    });
});


</script>
@endsection
