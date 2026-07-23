<?php

use App\Http\Controllers\Admin\CalendarSettingsController;
use App\Http\Controllers\Admin\CaseController;
use App\Http\Controllers\Admin\CaseUpdateController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailLogController;
use App\Http\Controllers\Admin\FinancialController;
use App\Http\Controllers\Admin\LegalAreaController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Ãrea interna (Administrador, Advogado, FuncionÃ¡rio) â guard "web", tabela "users".
Route::middleware('auth:web')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // GestÃ£o de usuÃ¡rios internos â sÃ³ Administrador (Policy/Gate "manage-users").
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except('show', 'destroy');
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
    });

    // MÃ³dulo de tarefas/prazos (Bloco 4). Rotas em portuguÃªs, conforme decisÃ£o do Bloco 3.
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

    // MÃ³dulo financeiro (Bloco 5). Substitui a rota temporÃ¡ria "test-financial-access" do
    // Bloco 2 â a Gate "view-financial" continua a mesma, sÃ³ passou a proteger telas reais.
    Route::middleware('can:view-financial')->prefix('financeiro')->name('financeiro.')->group(function () {
        Route::get('/', [FinancialController::class, 'index'])->name('index');
        Route::get('/criar', [FinancialController::class, 'create'])->name('create');
        Route::post('/', [FinancialController::class, 'store'])->name('store');
        Route::get('/{lancamento}/editar', [FinancialController::class, 'edit'])->name('edit');
        Route::put('/{lancamento}', [FinancialController::class, 'update'])->name('update');
    });

    // Clientes (Bloco 9: listagem/perfil; Bloco 10: CRUD completo, Gate "manage-clients").
    // Rotas de texto fixo ("criar") sempre antes da rota com parÃ¢metro ("{cliente}"), senÃ£o
    // o binding implÃ­cito do Eloquent tentaria resolver "criar" como um ID.
    Route::get('/clientes', [ClientController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/criar', [ClientController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClientController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/editar', [ClientController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClientController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClientController::class, 'destroy'])->name('clientes.destroy');
    Route::get('/clientes/{cliente}', [ClientController::class, 'show'])->name('clientes.show');

    // Processos (Bloco 9 â listagem/detalhe; Bloco 10: CRUD completo via LegalCasePolicy,
    // jÃ¡ criada no Bloco 2 e agora totalmente em uso).
    Route::get('/processos', [CaseController::class, 'index'])->name('processos.index');
    Route::get('/processos/criar', [CaseController::class, 'create'])->name('processos.create');
    Route::post('/processos', [CaseController::class, 'store'])->name('processos.store');
    Route::get('/processos/{processo}/editar', [CaseController::class, 'edit'])->name('processos.edit');
    Route::put('/processos/{processo}', [CaseController::class, 'update'])->name('processos.update');
    Route::delete('/processos/{processo}', [CaseController::class, 'destroy'])->name('processos.destroy');
    Route::get('/processos/{processo}', [CaseController::class, 'show'])->name('processos.show');

    // AtualizaÃ§Ãµes de processo (case_updates) â CRUD direto, sem depender de concluir
    // tarefa (Bloco 4). Mesma autorizaÃ§Ã£o da ediÃ§Ã£o do processo.
    Route::post('/processos/{processo}/atualizacoes', [CaseUpdateController::class, 'store'])->name('processos.atualizacoes.store');
    Route::get('/processos/{processo}/atualizacoes/{atualizacao}/editar', [CaseUpdateController::class, 'edit'])->name('processos.atualizacoes.edit');
    Route::put('/processos/{processo}/atualizacoes/{atualizacao}', [CaseUpdateController::class, 'update'])->name('processos.atualizacoes.update');
    Route::delete('/processos/{processo}/atualizacoes/{atualizacao}', [CaseUpdateController::class, 'destroy'])->name('processos.atualizacoes.destroy');

    // ServiÃ§os (Bloco 9 â listagem; Bloco 10: CRUD completo, Gate "manage-services").
    Route::get('/servicos', [ServiceController::class, 'index'])->name('servicos.index');
    Route::get('/servicos/criar', [ServiceController::class, 'create'])->name('servicos.create');
    Route::post('/servicos', [ServiceController::class, 'store'])->name('servicos.store');
    Route::get('/servicos/{servico}/editar', [ServiceController::class, 'edit'])->name('servicos.edit');
    Route::put('/servicos/{servico}', [ServiceController::class, 'update'])->name('servicos.update');
    Route::delete('/servicos/{servico}', [ServiceController::class, 'destroy'])->name('servicos.destroy');

    // Ãreas jurÃ­dicas: CRUD completo, Gate "manage-legal-areas" (sÃ³ Administrador para
    // criar/editar/excluir; listagem aberta a todos os perfis internos).
    Route::get('/areas-juridicas', [LegalAreaController::class, 'index'])->name('areas-juridicas.index');
    Route::get('/areas-juridicas/criar', [LegalAreaController::class, 'create'])->name('areas-juridicas.create');
    Route::post('/areas-juridicas', [LegalAreaController::class, 'store'])->name('areas-juridicas.store');
    Route::get('/areas-juridicas/{area}/editar', [LegalAreaController::class, 'edit'])->name('areas-juridicas.edit');
    Route::put('/areas-juridicas/{area}', [LegalAreaController::class, 'update'])->name('areas-juridicas.update');
    Route::delete('/areas-juridicas/{area}', [LegalAreaController::class, 'destroy'])->name('areas-juridicas.destroy');

    // Auditoria de e-mails automÃ¡ticos (Bloco 6) â sÃ³ Administrador (Gate "view-email-logs").
    Route::middleware('can:view-email-logs')->prefix('emails')->name('emails.')->group(function () {
        Route::get('/', [EmailLogController::class, 'index'])->name('index');
        Route::post('/{email}/reenviar', [EmailLogController::class, 'resend'])->name('resend');
    });

    // ConexÃ£o com Google Calendar (Bloco 7) â cada usuÃ¡rio interno conecta a prÃ³pria conta,
    // sem Gate de restriÃ§Ã£o (Administrador, Advogado e FuncionÃ¡rio podem conectar a si
    // mesmos).
    Route::prefix('configuracoes/agenda')->name('configuracoes.agenda.')->group(function () {
        Route::get('/', [CalendarSettingsController::class, 'edit'])->name('edit');
        Route::get('/conectar', [CalendarSettingsController::class, 'connect'])->name('connect');
        Route::get('/callback', [CalendarSettingsController::class, 'callback'])->name('callback');
        Route::post('/desconectar', [CalendarSettingsController::class, 'disconnect'])->name('disconnect');
    });

    // RelatÃ³rios automÃ¡ticos (Bloco 8) â sÃ³ Administrador (Gate "view-reports").
    Route::middleware('can:view-reports')->prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/download', [ReportController::class, 'download'])->name('download');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/admin_auth.php';
require __DIR__.'/portal_auth.php';
require __DIR__.'/portal.php';
