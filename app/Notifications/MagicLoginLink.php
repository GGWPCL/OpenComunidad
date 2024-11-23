<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MagicLoginLink extends Notification
{
    protected $url;
    protected $isNewUser;

    public function __construct(string $url, bool $isNewUser = false)
    {
        $this->url = $url;
        $this->isNewUser = $isNewUser;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject($this->isNewUser ? 'Welcome to Open Comunidad!' : 'Your Magic Login Link');

        if ($this->isNewUser) {
            $message->line('Welcome to Open Comunidad! Click the button below to access your new account.')
                   ->line('We\'re excited to have you join our community.');
        } else {
            $message->line('Click the button below to log in to your account.');
        }

        return $message
            ->action('Log In', $this->url)
            ->line('This login link will expire in ' . config('laravel-passwordless-login.login_route_expires') . ' minutes.')
            ->line('If you did not request this login link, no action is required.');
    }
}
