<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email Address - DulhaDulhan')
            ->greeting('Welcome to DulhaDulhan! ❤️')
            ->line('Thank you for registering with DulhaDulhan.')
            ->line('Please verify your email address to activate your account and start creating your matrimonial profile.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('This verification link will expire in 60 minutes.')
            ->line('If you did not create this account, you can safely ignore this email.')
            ->salutation('Regards,' . PHP_EOL . 'Team DulhaDulhan');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return [
            'title' => 'Verify Your Email Address - DulhaDulhan',
            'message' => 'Please verify your email address to activate your account and start creating your matrimonial profile.',
            'action_url' => $verificationUrl,
            'expires_in_minutes' => 60,
        ];
    }

    protected function verificationUrl($notifiable)
    {
        $url = parent::verificationUrl($notifiable);
        $parsed = parse_url($url);

        if (! $parsed || ! isset($parsed['scheme'], $parsed['host'])) {
            return $url;
        }

        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $host = $parsed['host'];
        $path = $parsed['path'] ?? '';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

        return $parsed['scheme'] . '://' . $host . $path . $query . $fragment;
    }
}
