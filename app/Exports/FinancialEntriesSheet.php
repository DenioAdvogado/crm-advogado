<?php

namespace App\Exports;

use App\Services\ReportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Uma aba por moeda (Bloco 8/Bloco 5: nunca somar BRL com EUR) — instanciada duas vezes em
 * AdminReportExport, uma para "BRL" e outra para "EUR".
 */
class FinancialEntriesSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private string $currency)
    {
    }

    public function collection(): Collection
    {
        $reportService = app(ReportService::class);

        $rows = $reportService->financialEntries($this->currency)->map(fn (array $row) => [
            $row['client'], $row['case_number'], $row['description'],
            $row['amount'], $row['due_date'], $row['status'],
        ]);

        // Linha em branco + totais por cliente, ao final da mesma aba — mantém o relatório
        // financeiro simples (uma aba por moeda) em vez de criar uma aba extra só para
        // totais.
        $rows->push(['', '', '', '', '', '']);
        $rows->push(['Total a receber por cliente (pendente + atrasado)', '', '', '', '', '']);

        foreach ($reportService->financialTotalsByClient($this->currency) as $total) {
            $rows->push([$total['client'], '', '', $total['total'], '', '']);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Cliente', 'Processo', 'Descrição', 'Valor ('.$this->currency.')', 'Vencimento', 'Status'];
    }

    public function title(): string
    {
        return 'Financeiro '.$this->currency;
    }
}
