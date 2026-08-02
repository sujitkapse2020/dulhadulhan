<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . config('app.name') . ' — Your Journey Begins',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.welcome-mail',
            with: [
                'firstName' => $this->user->name,
                'profileUrl' => config('app.frontend_url') . '/profile/complete',
                'supportEmail' => config('mail.support_address', 'support@example.com'),
            ],
        );
    }
}