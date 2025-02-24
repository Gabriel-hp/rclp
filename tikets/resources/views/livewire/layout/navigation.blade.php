<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('dashboard') }}" wire:navigate>
            Logicpro
        </a>

        <!-- Botão de Toggle para Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu de Navegação -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Link para o Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}" wire:navigate>
                        Dashboard
                    </a>
                </li>
            </ul>

            <!-- Dropdown do Usuário -->
            <ul class="navbar-nav ms-auto">
                <!-- Link para o Perfil -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile') }}" wire:navigate>
                        {{ auth()->user()->name }}
                    </a>
                </li>
                <!-- Botão de Logout -->
                <li class="nav-item">
                    <button class="nav-link btn btn-link" wire:click="logout">
                        Sair
                    </button>
                </li>

                <button id="toggle-theme" class="btn-mde">
                    <span class="icon">☀️</span> Alternar Tema
                </button>

            </ul>
        </div>
    </div>
</nav>