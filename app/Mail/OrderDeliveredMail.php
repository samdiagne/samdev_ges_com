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

class OrderDeliveredMail extends Mailable implements ShouldQueue
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
            subject: '🎉 Votre commande ' . $this->commande->reference . ' a été livrée !',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.commandes.delivered',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
