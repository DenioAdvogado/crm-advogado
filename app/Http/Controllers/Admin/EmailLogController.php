<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendCaseUpdateEmail;
use App\Models\EmailLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailLogController extends Controller
{
    public function index(): View
    {
        $this->authorize('view-email-logs');

        $logs = EmailLog::with(['client', 'caseUpdate.case'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.emails.index', ['logs' => $logs]);
    }

    /**
     * Reenvio manual em caso de falha (Bloco 6) — redespacha o mesmo Job para o EmailLog
     * existente, sem criar um novo registro.
     */
    public function resend(EmailLog $email): RedirectResponse
    {
        $this->authorize('view-email-logs');

        $email->update(['status' => 'pending', 'error_message' => null]);

        SendCaseUpdateEmail::dispatch($email->id);

        return redirect()->route('admin.emails.index')->with('status', 'Reenvio agendado na fila.');
    }
}
