<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Schedule $schedule): void
    {
        // Agendando seu comando 'tickets:atualizar' a cada 30 minutos
        $schedule->call(fn () => \Artisan::call('tickets:atualizar'))->everyThirtyMinutes();
    }
}
