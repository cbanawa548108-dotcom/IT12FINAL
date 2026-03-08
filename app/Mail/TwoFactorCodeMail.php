<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $fname,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your One-Time Login Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.two-factor-code',
            with: [
                'code'  => $this->code,
                'fname' => $this->fname,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}