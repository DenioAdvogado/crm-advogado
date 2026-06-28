<?php

namespace App\Exports;

use App\Services\ReportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PendingDeadlinesSheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection(): Collection
    {
        return app(ReportService::class)->pendingDeadlines()->map(fn (array $row) => [
            $row['client'],
            $row['country'],
            $row['case_number'],
            $row['legal_area'],
            $row['label'],
            $row['responsible'],
            $row['due_date'],
            $row['status'],
            $row['days_until_due'],
        ]);
    }

    public function headings(): array
    {
        return [
            'Cliente', 'País', 'Processo', 'Área Jurídica', 'Serviço/Tarefa',
            'Responsável', 'Prazo', 'Status', 'Dias até o prazo',
        ];
    }

    public function title(): string
    {
        return 'Prazos Pendentes';
    }
}
