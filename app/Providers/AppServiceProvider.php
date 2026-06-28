<?php

namespace App\Providers;

use App\Models\User;
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
    }
}
