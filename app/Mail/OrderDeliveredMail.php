<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderDeliveredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Votre commande ' . $this->order->reference . ' a été livrée !',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.delivered',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
