<?php

use App\Http\Controllers\Admin\CalendarSettingsController;
use App\Http\Controllers\Admin\CaseController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailLogController;
use App\Http\Controllers\Admin\FinancialController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Área interna (Administrador, Advogado, Funcionário) — guard "web", tabela "users".
Route::middleware('auth:web')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Gestão de usuários internos — só Administrador (Policy/Gate "manage-users").
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except('show', 'destroy');
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
    });

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

    // Módulo financeiro (Bloco 5). Substitui a rota temporária "test-financial-access" do
    // Bloco 2 — a Gate "view-financial" continua a mesma, só passou a proteger telas reais.
    Route::middleware('can:view-financial')->prefix('financeiro')->name('financeiro.')->group(function () {
        Route::get('/', [FinancialController::class, 'index'])->name('index');
        Route::get('/criar', [FinancialController::class, 'create'])->name('create');
        Route::post('/', [FinancialController::class, 'store'])->name('store');
        Route::get('/{lancamento}/editar', [FinancialController::class, 'edit'])->name('edit');
        Route::put('/{lancamento}', [FinancialController::class, 'update'])->name('update');
    });

    // Clientes (Bloco 9: listagem/perfil; Bloco 10: CRUD completo, Gate "manage-clients").
    // Rotas de texto fixo ("criar") sempre antes da rota com parâmetro ("{cliente}"), senão
    // o binding implícito do Eloquent tentaria resolver "criar" como um ID.
    Route::get('/clientes', [ClientController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/criar', [ClientController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClientController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/editar', [ClientController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClientController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClientController::class, 'destroy'])->name('clientes.destroy');
    Route::get('/clientes/{cliente}', [ClientController::class, 'show'])->name('clientes.show');

    // Processos (Bloco 9 — listagem/detalhe; Bloco 10: CRUD completo via LegalCasePolicy,
    // já criada no Bloco 2 e agora totalmente em uso).
    Route::get('/processos', [CaseController::class, 'index'])->name('processos.index');
    Route::get('/processos/criar', [CaseController::class, 'create'])->name('processos.create');
    Route::post('/processos', [CaseController::class, 'store'])->name('processos.store');
    Route::get('/processos/{processo}/editar', [CaseController::class, 'edit'])->name('processos.edit');
    Route::put('/processos/{processo}', [CaseController::class, 'update'])->name('processos.update');
    Route::delete('/processos/{processo}', [CaseController::class, 'destroy'])->name('processos.destroy');
    Route::get('/processos/{processo}', [CaseController::class, 'show'])->name('processos.show');

    // Serviços (Bloco 9 — listagem; Bloco 10: CRUD completo, Gate "manage-services").
    Route::get('/servicos', [ServiceController::class, 'index'])->name('servicos.index');
    Route::get('/servicos/criar', [ServiceController::class, 'create'])->name('servicos.create');
    Route::post('/servicos', [ServiceController::class, 'store'])->name('servicos.store');
    Route::get('/servicos/{servico}/editar', [ServiceController::class, 'edit'])->name('servicos.edit');
    Route::put('/servicos/{servico}', [ServiceController::class, 'update'])->name('servicos.update');
    Route::delete('/servicos/{servico}', [ServiceController::class, 'destroy'])->name('servicos.destroy');

    // Auditoria de e-mails automáticos (Bloco 6) — só Administrador (Gate "view-email-logs").
    Route::middleware('can:view-email-logs')->prefix('emails')->name('emails.')->group(function () {
        Route::get('/', [EmailLogController::class, 'index'])->name('index');
        Route::post('/{email}/reenviar', [EmailLogController::class, 'resend'])->name('resend');
    });

    // Conexão com Google Calendar (Bloco 7) — cada usuário interno conecta a própria conta,
    // sem Gate de restrição (Administrador, Advogado e Funcionário podem conectar a si
    // mesmos).
    Route::prefix('configuracoes/agenda')->name('configuracoes.agenda.')->group(function () {
        Route::get('/', [CalendarSettingsController::class, 'edit'])->name('edit');
        Route::get('/conectar', [CalendarSettingsController::class, 'connect'])->name('connect');
        Route::get('/callback', [CalendarSettingsController::class, 'callback'])->name('callback');
        Route::post('/desconectar', [CalendarSettingsController::class, 'disconnect'])->name('disconnect');
    });

    // Relatórios automáticos (Bloco 8) — só Administrador (Gate "view-reports").
    Route::middleware('can:view-reports')->prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/download', [ReportController::class, 'download'])->name('download');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/admin_auth.php';
require __DIR__.'/portal_auth.php';
require __DIR__.'/portal.php';
