<?php

use Illuminate\Support\Facades\Route;

// Área autenticada do Portal do Cliente — guard "client", tabela "clients".
// Telas reais do portal vêm no Bloco 3; aqui só a rota mínima para validar o login.
Route::middleware('auth:client')->prefix('portal')->name('portal.')->group(function () {
    Route::get('/dashboard', function () {
        return view('portal-dashboard');
    })->name('dashboard');
});
