<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DeleteTaskGoogleCalendarEvent;
use App\Jobs\SyncTaskWithGoogleCalendar;
use App\Models\CaseUpdate;
use App\Models\LegalCase;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use App\Services\ProductivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Task::class);

        $user = Auth::guard('web')->user();

        $tasks = Task::visibleTo($user)
            ->with(['responsible', 'case.client', 'service.client']);

        if ($request->filled('responsible_id')) {
            $tasks->where('responsible_id', $request->input('responsible_id'));
        }

        if ($request->filled('status')) {
            $tasks->where('status', $request->input('status'));
        }

        match ($request->input('deadline')) {
            'today' => $tasks->whereDate('due_date', Carbon::today()),
            'this_week' => $tasks->whereBetween('due_date', [
                Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(),
            ]),
            'overdue' => $tasks->where('status', 'overdue'),
            default => null,
        };

        $tasks = $tasks->orderBy('due_date')->paginate(20)->withQueryString();

        // Lista de responsáveis para o filtro — só faz sentido mostrar para quem pode ver
        // tarefas de mais de uma pessoa (administrador ou advogado com can_view_all_cases).
        $responsibleOptions = ($user->isAdministrator() || ($user->isLawyer() && $user->can_view_all_cases))
            ? User::where('active', true)->orderBy('name')->get()
            : collect();

        return view('admin.tasks.index', [
            'tasks' => $tasks,
            'responsibleOptions' => $responsibleOptions,
            'filters' => $request->only(['responsible_id', 'status', 'deadline']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Task::class);

        return view('admin.tasks.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Task::class);

        $validated = $this->validateTask($request);

        $task = Task::create([...$validated, 'status' => 'pending']);

        // Bloco 7: sincroniza com o Google Calendar do responsável em fila — não bloqueia
        // esta resposta nem falha a criação da tarefa se a integração estiver fora do ar.
        SyncTaskWithGoogleCalendar::dispatch($task->id);

        return redirect()->route('admin.tarefas.index')->with('status', 'Tarefa criada com sucesso.');
    }

    public function edit(Task $tarefa): View
    {
        $this->authorize('update', $tarefa);

        return view('admin.tasks.edit', [...$this->formOptions(), 'task' => $tarefa]);
    }

    public function update(Request $request, Task $tarefa): RedirectResponse
    {
        $this->authorize('update', $tarefa);

        $validated = $this->validateTask($request, $tarefa);

        $tarefa->update($validated);

        // Bloco 7: se a edição concluiu a tarefa por aqui (em vez de usar o fluxo dedicado
        // de "Concluir"), remove o evento; senão, sincroniza o evento (prazo pode ter
        // mudado). Em ambos os casos, em fila.
        if ($tarefa->status === 'completed') {
            DeleteTaskGoogleCalendarEvent::dispatch($tarefa->id);
        } else {
            SyncTaskWithGoogleCalendar::dispatch($tarefa->id);
        }

        return redirect()->route('admin.tarefas.index')->with('status', 'Tarefa atualizada com sucesso.');
    }

    public function completeForm(Task $tarefa): View
    {
        $this->authorize('update', $tarefa);

        return view('admin.tasks.complete', ['task' => $tarefa]);
    }

    /**
     * Conclui a tarefa e, se marcado, registra a intenção de notificar o cliente
     * (Bloco 4 — o envio de e-mail de fato só é implementado no Bloco 6).
     */
    public function complete(Request $request, Task $tarefa): RedirectResponse
    {
        $this->authorize('update', $tarefa);

        $validated = $request->validate([
            'update_description' => ['nullable', 'string'],
            'notify_client' => ['nullable', 'boolean'],
        ]);

        // completed_at não está em $fillable (não deve vir de input do usuário), por isso
        // forceFill() em vez de update().
        $tarefa->forceFill([
            'status' => 'completed',
            'completed_at' => now(),
        ])->save();

        // Bloco 7: tarefa concluída não precisa mais aparecer na agenda do responsável.
        DeleteTaskGoogleCalendarEvent::dispatch($tarefa->id);

        $relatedCase = $tarefa->relatedCase();

        if ($relatedCase && ! empty($validated['update_description'])) {
            CaseUpdate::create([
                'case_id' => $relatedCase->id,
                'author_id' => Auth::guard('web')->id(),
                'description' => $validated['update_description'],
                'notify_client' => $request->boolean('notify_client'),
            ]);
        }

        return redirect()->route('admin.tarefas.index')->with('status', 'Tarefa concluída com sucesso.');
    }

    public function productivity(): View
    {
        $this->authorize('view-productivity');

        $report = app(ProductivityService::class)->buildReport();

        return view('admin.tasks.productivity', ['report' => $report]);
    }

    private function formOptions(): array
    {
        return [
            'cases' => LegalCase::with('client')->orderByDesc('id')->get(),
            'services' => Service::with('client')->orderByDesc('id')->get(),
            'users' => User::where('active', true)->orderBy('name')->get(),
        ];
    }

    private function validateTask(Request $request, ?Task $task = null): array
    {
        // "status" só faz parte do formulário ao editar (na criação a tarefa sempre nasce
        // "pending", forçado em store()) — por isso só é exigido quando $task existe.
        return $request->validate([
            'case_id' => [
                'nullable', 'required_without:service_id', Rule::exists('cases', 'id'),
            ],
            'service_id' => [
                'nullable', 'required_without:case_id', Rule::exists('services', 'id'),
            ],
            'responsible_id' => ['required', Rule::exists('users', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date'],
            'status' => [
                $task ? 'required' : 'nullable',
                Rule::in(['pending', 'in_progress', 'completed', 'overdue']),
            ],
        ]);
    }
}
