<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Calcula métricas de produtividade de tarefas (Bloco 4). Centralizada aqui em vez de
 * direto no controller porque o Bloco 8 (relatórios automáticos) vai reaproveitar os mesmos
 * cálculos. O nome em português usado no texto do Bloco 4 ("ProdutividadeService") foi
 * traduzido para "ProductivityService" para seguir a convenção de classes em inglês
 * (CLAUDE.md) — mesmo padrão já aplicado a "notificar_cliente" -> "notify_client".
 */
class ProductivityService
{
    public function countCompletedToday(User $user): int
    {
        return Task::where('responsible_id', $user->id)
            ->whereDate('completed_at', Carbon::today())
            ->count();
    }

    /**
     * Semana anterior à atual: segunda a domingo, ambos antes da semana de hoje.
     */
    public function countCompletedLastWeek(User $user): int
    {
        [$start, $end] = $this->previousWeekRange();

        return Task::where('responsible_id', $user->id)
            ->whereBetween('completed_at', [$start, $end])
            ->count();
    }

    /**
     * Relatório com todos os usuários internos ativos: tarefas concluídas hoje e na
     * semana anterior. Usado em /admin/tarefas/produtividade (Bloco 4) e no Bloco 8.
     */
    public function buildReport(): Collection
    {
        [$start, $end] = $this->previousWeekRange();

        return User::where('active', true)
            ->orderBy('name')
            ->get()
            ->map(function (User $user) use ($start, $end) {
                return [
                    'user' => $user,
                    'completed_today' => Task::where('responsible_id', $user->id)
                        ->whereDate('completed_at', Carbon::today())
                        ->count(),
                    'completed_last_week' => Task::where('responsible_id', $user->id)
                        ->whereBetween('completed_at', [$start, $end])
                        ->count(),
                ];
            });
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function previousWeekRange(): array
    {
        $start = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        $end = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY);

        return [$start, $end];
    }
}
