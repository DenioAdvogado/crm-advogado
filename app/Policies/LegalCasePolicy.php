<?php

namespace App\Policies;

use App\Models\LegalCase;
use App\Models\User;

class LegalCasePolicy
{
    /**
     * Administrador vê tudo. Advogado vê os processos onde é responsável, ou todos se
     * "can_view_all_cases" estiver liberado pelo administrador (permissão configurável,
     * conforme pedido no Bloco 2). Funcionário também pode ver (a restrição do Bloco 2 é só
     * sobre o módulo financeiro, não sobre processos/tarefas/serviços).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LegalCase $legalCase): bool
    {
        if ($user->isAdministrator() || $user->isStaff()) {
            return true;
        }

        return $user->isLawyer()
            && ($legalCase->responsible_lawyer_id === $user->id || $user->can_view_all_cases);
    }

    public function create(User $user): bool
    {
        return $user->isAdministrator() || $user->isLawyer();
    }

    public function update(User $user, LegalCase $legalCase): bool
    {
        return $this->view($user, $legalCase);
    }

    public function delete(User $user, LegalCase $legalCase): bool
    {
        return $user->isAdministrator();
    }

    public function restore(User $user, LegalCase $legalCase): bool
    {
        return $user->isAdministrator();
    }

    public function forceDelete(User $user, LegalCase $legalCase): bool
    {
        return false;
    }
}
