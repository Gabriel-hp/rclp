<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


app()->booted(function () {
    $schedule = app(Schedule::class);
    
    // Agende seu comando
    $schedule->command('tickets:atualizar')->everyMinute();
});

