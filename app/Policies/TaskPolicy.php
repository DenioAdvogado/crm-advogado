<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Todos os perfis internos podem acessar a listagem — o filtro de QUAIS tarefas
     * aparecem é feito na query do controller (Task::visibleTo($user)), não aqui.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Administrador vê qualquer tarefa. Advogado vê a própria ou, se
     * "can_view_all_cases" estiver liberado (mesma flag do Bloco 2, reaproveitada aqui
     * para "ver tarefas de toda a equipe"), vê todas. Funcionário só vê as próprias —
     * sem exceção configurável, conforme pedido no Bloco 4.
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->isAdministrator()) {
            return true;
        }

        if ($user->isLawyer()) {
            return $task->responsible_id === $user->id || $user->can_view_all_cases;
        }

        return $task->responsible_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        return $this->view($user, $task);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isAdministrator();
    }

    public function restore(User $user, Task $task): bool
    {
        return $user->isAdministrator();
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
