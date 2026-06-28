<?php

use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Área interna (Administrador, Advogado, Funcionário) — guard "web", tabela "users".
Route::middleware('auth:web')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Gestão de usuários internos — só Administrador (Policy/Gate "manage-users").
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except('show', 'destroy');
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
    });

    // Rota TEMPORÁRIA de teste do Bloco 2, só para comprovar que a Policy/Gate do módulo
    // financeiro bloqueia "staff" sem a permissão "can_access_financial". Será substituída
    // pelas telas reais do módulo financeiro no Bloco 5.
    Route::get('/test-financial-access', function () {
        return 'OK: acesso financeiro permitido para '.auth('web')->user()->name;
    })->middleware('can:view-financial')->name('test-financial-access');

    // Módulo de tarefas/prazos (Bloco 4). Rotas em português, conforme decisão do Bloco 3.
    Route::prefix('tarefas')->name('tarefas.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/criar', [TaskController::class, 'create'])->name('create');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/produtividade', [TaskController::class, 'productivity'])->name('productivity');
        Route::get('/{tarefa}/editar', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{tarefa}', [TaskController::class, 'update'])->name('update');
        Route::get('/{tarefa}/concluir', [TaskController::class, 'completeForm'])->name('complete-form');
        Route::post('/{tarefa}/concluir', [TaskController::class, 'complete'])->name('complete');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/admin_auth.php';
require __DIR__.'/portal_auth.php';
require __DIR__.'/portal.php';
