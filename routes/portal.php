<?php

use App\Http\Controllers\Portal\CaseController;
use App\Http\Controllers\Portal\DashboardController;
use App\Http\Controllers\Portal\ProfileController;
use Illuminate\Support\Facades\Route;

// Área autenticada do Portal do Cliente — guard "client", tabela "clients".
Route::middleware('auth:client')->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/processos/{case}', [CaseController::class, 'show'])->name('processos.show');

    Route::get('/meus-dados', [ProfileController::class, 'edit'])->name('meus-dados.edit');
    Route::patch('/meus-dados', [ProfileController::class, 'update'])->name('meus-dados.update');
});
