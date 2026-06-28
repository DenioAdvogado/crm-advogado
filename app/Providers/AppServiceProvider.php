<?php

namespace App\Providers;

use App\Models\CaseUpdate;
use App\Models\User;
use App\Observers\CaseUpdateObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        // Gestão de usuários internos: só Administrador (Bloco 2).
        Gate::define('manage-users', function (User $user) {
            return $user->isAdministrator();
        });

        // Acesso ao módulo financeiro (Bloco 5 ainda não existe — Gate já fica pronta):
        // - Administrador: sempre.
        // - Advogado: sempre (acompanha o financeiro dos seus próprios clientes).
        // - Funcionário: só se o administrador liberar via "can_access_financial".
        Gate::define('view-financial', function (User $user) {
            if ($user->isAdministrator() || $user->isLawyer()) {
                return true;
            }

            return $user->isStaff() && $user->can_access_financial;
        });

        // Painel de produtividade (Bloco 4): só Administrador e Advogado.
        Gate::define('view-productivity', function (User $user) {
            return $user->isAdministrator() || $user->isLawyer();
        });

        // Auditoria de e-mails (Bloco 6): só Administrador.
        Gate::define('view-email-logs', function (User $user) {
            return $user->isAdministrator();
        });

        // Bloco 6: dispara o envio de e-mail ao cliente quando uma atualização de processo
        // é criada com notify_client = true.
        CaseUpdate::observe(CaseUpdateObserver::class);
    }
}
