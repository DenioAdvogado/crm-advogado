<?php

namespace App\Services;

use App\Models\FinancialEntry;
use App\Models\Service;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Monta os dados do relatório automático (Bloco 8) — separado da classe de export
 * (`App\Exports\AdminReportExport`) para que a lógica de consulta/cálculo não fique
 * misturada com a formatação de planilha do maatwebsite/excel.
 */
class ReportService
{
    /**
     * Todos os serviços e tarefas ainda não concluídos, juntos em uma lista só (Bloco 8
     * pediu "todos os serviços e tarefas pendentes" em uma única aba), ordenados por prazo
     * mais próximo primeiro.
     */
    public function pendingDeadlines(): Collection
    {
        $services = Service::with(['client', 'case.legalArea', 'responsible'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->get()
            ->map(function (Service $service) {
                return $this->row(
                    client: $service->client,
                    case: $service->case,
                    label: $service->description,
                    type: 'Serviço',
                    responsible: $service->responsible,
                    dueDate: $service->execution_deadline,
                    status: $service->status,
                );
            });

        $tasks = Task::with(['case.legalArea', 'service.client', 'service.case.legalArea', 'responsible'])
            ->whereIn('status', ['pending', 'in_progress', 'overdue'])
            ->get()
            ->map(function (Task $task) {
                $relatedCase = $task->relatedCase();
                $client = $relatedCase?->client ?? $task->service?->client;

                return $this->row(
                    client: $client,
                    case: $relatedCase,
                    label: $task->title,
                    type: 'Tarefa',
                    responsible: $task->responsible,
                    dueDate: $task->due_date,
                    status: $task->status,
                );
            });

        return $services->concat($tasks)
            ->sortBy(fn (array $row) => $row['due_date_raw'] ?? Carbon::maxValue())
            ->values();
    }

    private function row($client, $case, string $label, string $type, $responsible, ?Carbon $dueDate, string $status): array
    {
        $daysUntilDue = $dueDate ? Carbon::today()->diffInDays($dueDate, false) : null;

        return [
            'client' => $client?->name ?? '—',
            'country' => $client?->country === 'Portugal' ? 'Portugal' : 'Brasil',
            'case_number' => $case?->case_number ?? '—',
            'legal_area' => $case?->legalArea?->name ?? '—',
            'label' => "[{$type}] {$label}",
            'responsible' => $responsible?->name ?? '—',
            'due_date' => $dueDate?->format('d/m/Y') ?? '—',
            'due_date_raw' => $dueDate,
            'status' => $this->translateStatus($status),
            'days_until_due' => $daysUntilDue,
        ];
    }

    private function translateStatus(string $status): string
    {
        return match ($status) {
            'pending' => 'Pendente',
            'in_progress' => 'Em andamento',
            'overdue' => 'Atrasado',
            default => $status,
        };
    }

    /**
     * Lançamentos pendentes/atrasados de uma moeda específica — nunca somados com a outra
     * moeda, conforme decisão já tomada no Bloco 5.
     */
    public function financialEntries(string $currency): Collection
    {
        return FinancialEntry::with(['client', 'case'])
            ->where('currency', $currency)
            ->whereIn('status', ['pending', 'overdue'])
            ->orderBy('due_date')
            ->get()
            ->map(fn (FinancialEntry $entry) => [
                'client' => $entry->client->name,
                'case_number' => $entry->case?->case_number ?? '—',
                'description' => $entry->description,
                'amount' => (float) $entry->amount,
                'due_date' => $entry->due_date?->format('d/m/Y') ?? '—',
                'status' => $entry->status === 'overdue' ? 'Atrasado' : 'Pendente',
            ]);
    }

    /**
     * Total pendente+atrasado por cliente, para a mesma moeda.
     */
    public function financialTotalsByClient(string $currency): Collection
    {
        return FinancialEntry::with('client')
            ->where('currency', $currency)
            ->whereIn('status', ['pending', 'overdue'])
            ->get()
            ->groupBy('client_id')
            ->map(fn (Collection $entries) => [
                'client' => $entries->first()->client->name,
                'total' => (float) $entries->sum('amount'),
            ])
            ->values();
    }
}
