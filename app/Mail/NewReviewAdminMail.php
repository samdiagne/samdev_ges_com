<?php

namespace App\Mail;

use App\Models\ProductReview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewReviewAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ProductReview $review) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⭐ Nouvel avis client — ' . $this->review->product->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reviews.admin-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
