<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewGivenNotification extends Notification
{
    use Queueable;

    protected $departmentName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($departmentName)
    {
        $this->departmentName = $departmentName;
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
                    ->subject('Um parecer foi dado a um requerimento')
                    ->greeting('Prezado(a)s da secretaria do ' . $this->departmentName . ',')
                    ->line('Um parecer foi dado a um requerimento de aproveitamento de estudos.')
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
