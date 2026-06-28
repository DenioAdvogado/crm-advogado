<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\GoogleCalendarService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Cria/atualiza o evento da tarefa no Google Calendar do responsável (Bloco 7). Roda em
 * fila (mesma infraestrutura do Bloco 6) para não travar a tela ao criar/editar tarefas —
 * a sincronização com o Google nunca deve ser pré-requisito para a operação principal.
 */
class SyncTaskWithGoogleCalendar implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $taskId)
    {
    }

    public function handle(GoogleCalendarService $calendar): void
    {
        $task = Task::find($this->taskId);

        if ($task) {
            $calendar->syncTask($task);
        }
    }
}
