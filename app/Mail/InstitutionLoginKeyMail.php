<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstitutionLoginKeyMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nama;
    public string $loginKey;

    public function __construct(string $nama, string $loginKey)
    {
        $this->nama = $nama;
        $this->loginKey = $loginKey;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kunci Login Lembaga Inkluvia',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lembaga.login_key',
            with: [
                'nama' => $this->nama,
                'loginKey' => $this->loginKey,
            ],
        );
    }
}


