<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\GoogleCalendarService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Remove o evento do Google Calendar quando a tarefa é concluída ou excluída (Bloco 7).
 * Usa withTrashed() porque "tasks" tem soft delete — o registro ainda existe na tabela
 * mesmo após "excluída".
 */
class DeleteTaskGoogleCalendarEvent implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $taskId)
    {
    }

    public function handle(GoogleCalendarService $calendar): void
    {
        $task = Task::withTrashed()->find($this->taskId);

        if ($task) {
            $calendar->deleteTaskEvent($task);
        }
    }
}
