<?php

namespace App\Jobs;

use App\Mail\CaseUpdateNotification;
use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Envia o e-mail de atualização de processo de forma assíncrona (Bloco 6) — não bloqueia a
 * tela do usuário interno enquanto o e-mail é enviado. Depende de um worker de fila rodando
 * (`php artisan queue:work`); ver CLAUDE.md sobre essa dependência em produção.
 */
class SendCaseUpdateEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $emailLogId)
    {
    }

    public function handle(): void
    {
        $emailLog = EmailLog::with('caseUpdate.case.client')->find($this->emailLogId);

        if (! $emailLog) {
            return;
        }

        try {
            Mail::to($emailLog->client->email)->send(new CaseUpdateNotification($emailLog->caseUpdate));

            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $exception) {
            $emailLog->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);
        }
    }
}
