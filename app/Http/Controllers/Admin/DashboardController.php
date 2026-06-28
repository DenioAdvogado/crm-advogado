<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\FinancialEntry;
use App\Models\LegalCase;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Dashboard consolidado (Bloco 9) — mesma rota para os três perfis, conteúdo adaptado
 * reaproveitando as Policies/Gates já existentes (Bloco 2 em diante), sem lógica de negócio
 * nova: só agrega dados que já existem nos módulos dos blocos anteriores.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::guard('web')->user();

        if ($user->isStaff()) {
            return view('dashboard', $this->staffData($user));
        }

        return view('dashboard', $this->adminOrLawyerData($user));
    }

    private function adminOrLawyerData($user): array
    {
        $cases = LegalCase::visibleTo($user);

        $clientIds = (clone $cases)->pluck('client_id')->unique();
        $activeClientsCount = Client::where('active', true)
            ->when(! $user->isAdministrator() && ! $user->isStaff(), fn ($query) => $query->whereIn('id', $clientIds))
            ->count();

        $casesInProgressCount = (clone $cases)->where('status', 'in_progress')->count();

        $taskQuery = Task::visibleTo($user);
        $tasksPendingCount = (clone $taskQuery)->whereIn('status', ['pending', 'in_progress'])->count();
        $tasksOverdueCount = (clone $taskQuery)->where('status', 'overdue')->count();

        $financialTotals = collect();
        $canViewFinancial = Gate::allows('view-financial');

        if ($canViewFinancial) {
            $financialQuery = FinancialEntry::where('type', 'income')->where('status', 'pending');

            if (! $user->isAdministrator()) {
                $financialQuery->whereIn('client_id', $clientIds);
            }

            $financialTotals = $financialQuery->selectRaw('currency, sum(amount) as total')
                ->groupBy('currency')
                ->pluck('total', 'currency');
        }

        return [
            'role' => $user->isAdministrator() ? 'administrator' : 'lawyer',
            'activeClientsCount' => $activeClientsCount,
            'casesInProgressCount' => $casesInProgressCount,
            'tasksPendingCount' => $tasksPendingCount,
            'tasksOverdueCount' => $tasksOverdueCount,
            'financialTotals' => $financialTotals,
            'canViewFinancial' => $canViewFinancial,
            'upcomingDeadlines' => $this->upcomingDeadlines($user, $clientIds, $canViewFinancial),
        ];
    }

    private function staffData($user): array
    {
        $taskQuery = Task::visibleTo($user);

        $canViewFinancial = Gate::allows('view-financial');

        return [
            'role' => 'staff',
            'tasksPendingCount' => (clone $taskQuery)->whereIn('status', ['pending', 'in_progress'])->count(),
            'tasksOverdueCount' => (clone $taskQuery)->where('status', 'overdue')->count(),
            'canViewFinancial' => $canViewFinancial,
            'financialTotals' => $canViewFinancial
                ? FinancialEntry::where('type', 'income')->where('status', 'pending')
                    ->selectRaw('currency, sum(amount) as total')->groupBy('currency')->pluck('total', 'currency')
                : collect(),
            'upcomingDeadlines' => $this->upcomingDeadlines($user, collect(), $canViewFinancial),
        ];
    }

    /**
     * Tarefas e (se aplicável) lançamentos financeiros com prazo nos próximos 7 dias,
     * juntos numa lista só, ordenados por data.
     */
    private function upcomingDeadlines($user, Collection $clientIds, bool $canViewFinancial): Collection
    {
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);

        $tasks = Task::visibleTo($user)
            ->whereIn('status', ['pending', 'in_progress', 'overdue'])
            ->whereBetween('due_date', [$today, $nextWeek])
            ->with('responsible')
            ->get()
            ->map(fn (Task $task) => [
                'type' => 'Tarefa',
                'label' => $task->title,
                'date' => $task->due_date,
            ]);

        $financialEntries = collect();

        if ($canViewFinancial) {
            $financialQuery = FinancialEntry::with('client')
                ->whereIn('status', ['pending', 'overdue'])
                ->whereBetween('due_date', [$today, $nextWeek]);

            if (! $user->isAdministrator() && ! $user->isStaff() && $clientIds->isNotEmpty()) {
                $financialQuery->whereIn('client_id', $clientIds);
            }

            $financialEntries = $financialQuery->get()->map(fn (FinancialEntry $entry) => [
                'type' => 'Financeiro',
                'label' => $entry->client->name.' — '.$entry->description,
                'date' => $entry->due_date,
            ]);
        }

        return $tasks->concat($financialEntries)->sortBy('date')->values();
    }
}
