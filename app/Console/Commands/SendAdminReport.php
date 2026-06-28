<?php

namespace App\Console\Commands;

use App\Exports\AdminReportExport;
use App\Mail\AdminReportMail;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

#[Signature('app:send-admin-report')]
#[Description('Gera a planilha de relatorio (prazos, financeiro, produtividade) e envia por e-mail ao administrador (Bloco 8).')]
class SendAdminReport extends Command
{
    public function handle(): int
    {
        $recipient = config('reports.admin_email');

        if (! $recipient) {
            $this->error('ADMIN_REPORT_EMAIL não configurado no .env — relatório não enviado.');

            return self::FAILURE;
        }

        $fileName = 'relatorio-'.now()->format('Y-m-d').'.xlsx';
        $binary = Excel::raw(new AdminReportExport(), \Maatwebsite\Excel\Excel::XLSX);

        Mail::to($recipient)->send(new AdminReportMail($binary, $fileName));

        $this->info("Relatório enviado para {$recipient}.");

        return self::SUCCESS;
    }
}
