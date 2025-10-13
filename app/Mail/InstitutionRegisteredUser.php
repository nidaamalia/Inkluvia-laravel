<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstitutionRegisteredUser extends Mailable
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
            subject: 'Terima Kasih, Pendaftaran Lembaga Diterima',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.institution.user',
            with: [
                'data' => $this->data,
            ],
        );
    }
}


