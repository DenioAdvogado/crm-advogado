<?php

namespace App\Observers;

use App\Jobs\SendCaseUpdateEmail;
use App\Models\CaseUpdate;
use App\Models\EmailLog;

class CaseUpdateObserver
{
    /**
     * Gatilho do Bloco 6: toda CaseUpdate criada com notify_client = true gera um
     * EmailLog (status "pending") e despacha o envio para a fila — o registro de auditoria
     * existe mesmo antes do worker processar o Job.
     */
    public function created(CaseUpdate $caseUpdate): void
    {
        if (! $caseUpdate->notify_client) {
            return;
        }

        $caseUpdate->loadMissing('case.client');
        $client = $caseUpdate->case?->client;

        if (! $client) {
            return;
        }

        $emailLog = EmailLog::create([
            'client_id' => $client->id,
            'case_update_id' => $caseUpdate->id,
            'status' => 'pending',
        ]);

        SendCaseUpdateEmail::dispatch($emailLog->id);
    }
}
