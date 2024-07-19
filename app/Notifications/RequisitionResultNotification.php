<?php

namespace App\Notifications;

use App\Models\Requisition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequisitionResultNotification extends Notification
{
    use Queueable;

    protected $studentUser;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($studentUser)
    {
        $this->studentUser = $studentUser;
        // $this->customMessage = $customMessage;
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
        // $studentUserNusp = $this->studentUser->codpes;

        // Requisition::where('nusp', );
        return (new MailMessage)
                    ->subject('Atualização no requerimento de aproveitamento de estudos')
                    ->greeting('Prezado(a) aluno(a) ' . $this->studentUser->name . ',')
                    ->line('O seu requerimento de aproveitamento de estudos foi atualizado. Para obter mais informações, acesse o site de requerimentos através do link abaixo.')
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
