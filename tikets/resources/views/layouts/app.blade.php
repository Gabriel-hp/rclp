<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Estilos -->
    @vite(['resources/css/app.css'])
</head>

<body>
<header class="bg-app  shadow-md p-0">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800 text-white">
                <h1 class="text-white">Logic Pro</h1>
            </a>
            <!-- Usuário e Logout -->
            <div class="flex items-center gap-4">
                <span class="text-gray-700 text-white">
                    {{ Auth::user()->name ?? 'Usuário' }}
                </span>

                <form method="POST" action="{{ url('/logout') }}">

                    @csrf
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">Sair</button>
                    </form>

                </form>
            </div>
        </div>
    </header>
    <main >
        @yield('content')
    </main>
</body>

</html>
