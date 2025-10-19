<?php

namespace App\Mail;

use App\Models\ResellerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResellerApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    /**
     * Create a new message instance.
     */
    public function __construct(ResellerApplication $application)
    {
        $this->application = $application;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Supplier Application Received',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reseller.submitted',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
    public function build()
    {
    return $this->from('support@yourdomain.com', 'Fish Market')
                ->subject('Supplier Application Submitted')
                ->view('emails.Supplier.submitted');
    }
}