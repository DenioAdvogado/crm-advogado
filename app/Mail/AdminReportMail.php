<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $spreadsheetBinary, public string $fileName)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Relatório diário — '.now()->format('d/m/Y'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-report');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->spreadsheetBinary, $this->fileName)
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
