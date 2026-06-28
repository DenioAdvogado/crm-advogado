<?php

namespace App\Console\Commands;

use App\Models\FinancialEntry;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('app:mark-overdue-financial-entries')]
#[Description('Marca como "atrasado" qualquer lançamento financeiro pendente com vencimento passado (Bloco 5).')]
class MarkOverdueFinancialEntries extends Command
{
    public function handle(): int
    {
        $count = FinancialEntry::where('status', 'pending')
            ->where('due_date', '<', Carbon::today())
            ->update(['status' => 'overdue']);

        $this->info("{$count} lançamento(s) marcado(s) como atrasado(s).");

        return self::SUCCESS;
    }
}
