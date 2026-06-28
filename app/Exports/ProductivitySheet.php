<?php

namespace App\Exports;

use App\Services\ProductivityService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductivitySheet implements FromCollection, WithHeadings, WithTitle
{
    public function collection(): Collection
    {
        return app(ProductivityService::class)->buildReport()->map(fn (array $row) => [
            $row['user']->name,
            $row['completed_today'],
            $row['completed_last_week'],
            $row['overdue_count'],
        ]);
    }

    public function headings(): array
    {
        return ['Usuário', 'Concluídas hoje', 'Concluídas na semana anterior', 'Em atraso atualmente'];
    }

    public function title(): string
    {
        return 'Produtividade';
    }
}
