<?php

use App\Http\Controllers\DashboardController;


Route::get('/gerar-relatorio', [DashboardController::class, 'generateDailyReport'])->name('gerar.relatorio');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

Route::get('/dashboard/update', [DashboardController::class, 'atualizarChamados']);



Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

    use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


require __DIR__.'/auth.php';