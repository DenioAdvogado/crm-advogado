<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Relatório completo do Bloco 8 — uma única planilha .xlsx com 4 abas: prazos pendentes,
 * financeiro BRL, financeiro EUR e produtividade. Optei por um arquivo só (em vez de um
 * arquivo por relatório) porque é mais simples de manter/anexar no e-mail diário e de
 * baixar manualmente em /admin/relatorios — decisão documentada no CLAUDE.md.
 */
class AdminReportExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new PendingDeadlinesSheet(),
            new FinancialEntriesSheet('BRL'),
            new FinancialEntriesSheet('EUR'),
            new ProductivitySheet(),
        ];
    }
}
