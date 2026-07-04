<?php

namespace App\Mail;

use App\Models\Avis;
use App\Models\Parametre;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewReviewAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $settings;

    public function __construct(public Avis $avis)
    {
        $this->settings = Parametre::first();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⭐ Nouvel avis client — ' . $this->avis->produit->nom,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.avis.admin-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
