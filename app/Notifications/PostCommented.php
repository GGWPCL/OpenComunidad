<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostCommented extends Notification
{
    use Queueable;

    public function __construct(
        private Post $post,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getEmailSubject())
            ->greeting($this->getGreeting())
            ->line('El post que estas siguiendo ha recibido un nuevo comentario.')
            ->line('Puedes verlo haciendo click en el siguiente botón.')
            ->action('Ver post', route('posts.show', $this->post))
            ->salutation('¡Saludos del equipo de OpenComunidad!');
    }

    private function getEmailSubject(): string
    {
        return 'El post que estás siguiendo ha recibido un nuevo comentario';
    }

    private function getGreeting(): string
    {
        return '¡Hola!';
    }
}
