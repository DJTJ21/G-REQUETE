<?php

namespace App\Mail;

use App\Models\Requete;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequeteRecueMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Requete $requete) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[G-REQUÊTES] Votre requête ' . $this->requete->ref_requete . ' a bien été reçue',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.requete-recue');
    }

    public function attachments(): array
    {
        return [];
    }
}
