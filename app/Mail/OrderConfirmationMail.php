<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\CompanySetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public CompanySetting $settings;

    public function __construct(public Order $order)
    {
        $this->settings = CompanySetting::getSettings();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmation de votre commande ' . $this->order->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
