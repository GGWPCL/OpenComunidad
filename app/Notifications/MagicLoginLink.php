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
            ->subject($this->getEmailSubject())
            ->greeting($this->getGreeting());

        if ($this->isNewUser) {
            $message->line('¡Bienvenido a OpenComunidad! Te damos la bienvenida a nuestra plataforma.')
                   ->line('Para comenzar a usar tu cuenta, simplemente haz clic en el botón de abajo.')
                   ->line('En OpenComunidad podrás:')
                   ->line('• Conectar con otros miembros')
                   ->line('• Participar en discusiones')
                   ->line('• Acceder a recursos exclusivos');
        } else {
            $message->line('Hemos recibido una solicitud de acceso para tu cuenta.')
                   ->line('Para iniciar sesión de forma segura, haz clic en el botón de abajo.');
        }

        return $message
            ->action('Iniciar Sesión', $this->url)
            ->line('⚠️ Este enlace expirará en ' . config('laravel-passwordless-login.login_route_expires') . ' minutos.')
            ->line('Si no solicitaste este enlace, puedes ignorar este correo de forma segura.')
            ->salutation('¡Saludos del equipo de OpenComunidad!');
    }

    private function getEmailSubject(): string
    {
        return $this->isNewUser
            ? '🎉 ¡Bienvenido a Open Comunidad!'
            : '🔐 Tu enlace de acceso seguro';
    }

    private function getGreeting(): string
    {
        return $this->isNewUser
            ? '¡Hola y bienvenido!'
            : '¡Hola!';
    }
}
