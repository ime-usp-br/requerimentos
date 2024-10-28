<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SGNotification extends Notification
{
    use Queueable;

    protected $sgUser;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($sgUser)
    {
        $this->sgUser = $sgUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Novo/atualização de um requerimento de aproveitamento de estudos')
                    ->greeting('Prezado(a) ' . $this->sgUser->name . ',')
                    ->line('Um requerimento de aproveitamento de estudos foi criado/atualizado. Para saber mais informações, acesse o site de requerimentos através do link abaixo.')
                    ->action('Requerimentos', url('/'))
                    ->salutation('Atenciosamente, Serviço de Graduação do IME-USP.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
