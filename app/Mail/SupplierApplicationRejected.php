<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplierApplicationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    /**
     * Create a new message instance.
     */
    public function __construct($application)
    {
        $this->application = $application;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Supplier Application Status Update',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reseller.supplier-rejected',
            with: [
                'application' => $this->application,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
        /**
     * Build the message (Laravel 9/10 compatibility)
     */
    public function build()
    {
        return $this->from('support@yourdomain.com', 'Fish Market')
                    ->subject('Supplier Application Rejected')
                    ->view('emails.reseller.rejected');
    }
}