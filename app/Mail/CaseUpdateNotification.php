<?php

namespace App\Mail;

use App\Models\CaseUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CaseUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CaseUpdate $caseUpdate)
    {
    }

    public function envelope(): Envelope
    {
        $subject = $this->caseUpdate->case->client->country === 'Portugal'
            ? 'Há uma atualização no seu processo'
            : 'Você tem uma atualização no seu processo';

        return new Envelope(subject: $subject);
    }

    /**
     * Template varia por país (Bloco 6): pt_BR ("Olá") vs pt_PT ("Caro(a)") — mesma
     * estrutura, só o vocabulário/tom muda. Por segurança, o corpo do e-mail não traz dados
     * sensíveis do processo (valores financeiros, descrição completa do caso) — só o aviso
     * de que há atualização e o link para o cliente logar no portal e ver o detalhe.
     */
    public function content(): Content
    {
        $client = $this->caseUpdate->case->client;

        $view = $client->country === 'Portugal'
            ? 'emails.case-update-pt'
            : 'emails.case-update-br';

        return new Content(
            view: $view,
            with: [
                'clientName' => $client->name,
                'caseNumber' => $this->caseUpdate->case->case_number,
                'updateSummary' => $this->caseUpdate->description,
                'portalLoginUrl' => route('portal.login'),
            ],
        );
    }
}
