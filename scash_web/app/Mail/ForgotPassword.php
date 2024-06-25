<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $details;
    protected $web;

    /**
     * Create a new message instance.
     */
    public function __construct($details, $web='web')
    {
        $this->details = $details;
        $this->web = $web;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Forgot Password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if($this->web == 'API'){
            $this->details['email_link'] = route('guest.auth.resetPassword', ['email' => Crypt::encryptString($this->details['email'])]);
        } else {
            $this->details['email_link'] = route('merchant.auth.resetPassword', ['email' => Crypt::encryptString($this->details['email'])]);
        }

        return new Content(
            view: 'emails.ForgotPassword',
            with: ['details' => $this->details]
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
}
