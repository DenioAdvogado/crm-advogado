<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminReportExport;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        $this->authorize('view-reports');

        return view('admin.reports.index', [
            'adminEmail' => config('reports.admin_email'),
            'sendTime' => config('reports.send_time'),
        ]);
    }

    /**
     * Geração manual da planilha (Bloco 8) — sem precisar esperar o envio diário
     * agendado, útil para testes e para o dia a dia.
     */
    public function download(): BinaryFileResponse
    {
        $this->authorize('view-reports');

        $fileName = 'relatorio-'.now()->format('Y-m-d-His').'.xlsx';

        return Excel::download(new AdminReportExport(), $fileName);
    }
}
