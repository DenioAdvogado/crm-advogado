<?php

use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Não há login/registro "genérico" neste arquivo — são dois logins separados, ver
// routes/admin_auth.php (equipe interna) e routes/portal_auth.php (clientes). Aqui ficam só
// as rotas de conta usadas DEPOIS de autenticado no guard "web" (equipe interna), reaproveitadas
// da página de perfil padrão do Breeze. Não há auto-registro: usuários internos são criados
// pelo Administrador (tela de gestão de usuários); clientes serão tratados no Bloco 3.
Route::middleware('auth:web')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
});
