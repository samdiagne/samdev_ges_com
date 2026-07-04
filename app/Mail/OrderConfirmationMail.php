<?php

namespace App\Mail;

use App\Models\Commande;
use App\Models\Parametre;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $settings;

    public function __construct(public Commande $commande)
    {
        $this->settings = Parametre::first();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de votre commande ' . $this->commande->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.commandes.confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
