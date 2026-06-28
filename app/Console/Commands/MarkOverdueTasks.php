<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('app:mark-overdue-tasks')]
#[Description('Marca como "atrasada" qualquer tarefa pendente/em andamento com prazo vencido (Bloco 4).')]
class MarkOverdueTasks extends Command
{
    public function handle(): int
    {
        $count = Task::whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::now())
            ->update(['status' => 'overdue']);

        $this->info("{$count} tarefa(s) marcada(s) como atrasada(s).");

        return self::SUCCESS;
    }
}
