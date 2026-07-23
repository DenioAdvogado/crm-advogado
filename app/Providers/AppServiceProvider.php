<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;

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
        // Forçar HTTPS em produção (atrás do proxy Traefik)
        if (str_starts_with((string) config('app.url'), 'https')) {
            URL::forceScheme('https');
        }

        // GestÃ£o de usuÃ¡rios internos: sÃ³ Administrador (Bloco 2).
        Gate::define('manage-users', function (User $user) {
            return $user->isAdministrator();
        });

        // Acesso ao mÃ³dulo financeiro (Bloco 5 ainda nÃ£o existe â Gate jÃ¡ fica pronta):
        // - Administrador: sempre.
        // - Advogado: sempre (acompanha o financeiro dos seus prÃ³prios clientes).
        // - FuncionÃ¡rio: sÃ³ se o administrador liberar via "can_access_financial".
        Gate::define('view-financial', function (User $user) {
            if ($user->isAdministrator() || $user->isLawyer()) {
                return true;
            }

            return $user->isStaff() && $user->can_access_financial;
        });

        // Painel de produtividade (Bloco 4): sÃ³ Administrador e Advogado.
        Gate::define('view-productivity', function (User $user) {
            return $user->isAdministrator() || $user->isLawyer();
        });

        // Auditoria de e-mails (Bloco 6): sÃ³ Administrador.
        Gate::define('view-email-logs', function (User $user) {
            return $user->isAdministrator();
        });

        // RelatÃ³rios automÃ¡ticos (Bloco 8): sÃ³ Administrador.
        Gate::define('view-reports', function (User $user) {
            return $user->isAdministrator();
        });

        // Cadastro de clientes (Bloco 10 â completa o CRUD que faltava do Bloco 9):
        // Administrador e Advogado podem criar/editar; FuncionÃ¡rio sÃ³ visualiza.
        Gate::define('manage-clients', function (User $user) {
            return $user->isAdministrator() || $user->isLawyer();
        });

        // Cadastro de serviÃ§os (Bloco 10): qualquer perfil interno pode criar/editar â nÃ£o
        // hÃ¡ regra de "dono" para serviÃ§os em nenhum bloco anterior, diferente de
        // tarefas/processos.
        Gate::define('manage-services', function (User $user) {
            return true;
        });

        // Cadastro de Ã¡reas jurÃ­dicas (pedido do usuÃ¡rio apÃ³s o Bloco 10, para fechar a
        // lacuna de "todo cadastro precisa ser dinÃ¢mico"): sÃ³ Administrador â Ã© uma tabela
        // de apoio/taxonomia, nÃ£o um registro do dia a dia da operaÃ§Ã£o como cliente/
        // processo/serviÃ§o.
        Gate::define('manage-legal-areas', function (User $user) {
            return $user->isAdministrator();
        });

        // Bloco 6: dispara o envio de e-mail ao cliente quando uma atualizaÃ§Ã£o de processo
        // Ã© criada com notify_client = true.
        CaseUpdate::observe(CaseUpdateObserver::class);
    }
}
