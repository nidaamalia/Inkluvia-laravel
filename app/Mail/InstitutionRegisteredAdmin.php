<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstitutionRegisteredAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran Lembaga Baru',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.institution.admin',
            with: [
                'data' => $this->data,
            ],
        );
    }
}


