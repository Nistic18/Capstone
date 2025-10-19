<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplierApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $defaultPassword;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $defaultPassword = null)
    {
        $this->user = $user;
        $this->defaultPassword = $defaultPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Supplier Application Approved - Welcome!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reseller.supplier-approved',
            with: [
                'user' => $this->user,
                'defaultPassword' => $this->defaultPassword,
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
                    ->subject('🎉 Supplier Application Approved - Welcome!')
                    ->view('emails.reseller.supplier-approved')
                    ->with([
                        'user' => $this->user,
                        'defaultPassword' => $this->defaultPassword,
                    ]);
    }
}