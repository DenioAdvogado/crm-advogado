<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Criação de tarefa e mudança automática de status para "atrasada" (Bloco 4): o Command
 * agendado app:mark-overdue-tasks precisa marcar como "overdue" toda tarefa pendente com
 * due_date no passado.
 */
class TaskOverdueTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_creation_and_automatic_overdue_marking(): void
    {
        $responsible = User::factory()->create(['access_level' => 'lawyer']);

        $task = Task::create([
            'responsible_id' => $responsible->id,
            'title' => 'Protocolar petição',
            'due_date' => Carbon::yesterday(),
            'status' => 'pending',
        ]);

        $this->assertSame('pending', $task->status);

        $this->artisan('app:mark-overdue-tasks')->assertExitCode(0);

        $this->assertSame('overdue', $task->refresh()->status);
    }
}
