<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordReseted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        # TODO: implement the real url to create a ticket when exists.
        return (new MailMessage())
            ->subject(Lang::get('Password Reseted Notification')) // @phpstan-ignore-line
            ->line(Lang::get('You are receiving this email because your password has been changed.'))
            ->line(Lang::get('If you did not recognize this action, please contact us and create a ticket as soon as possible.'))
            ->action(Lang::get('Create Ticket'), 'fakeurl.com'); // @phpstan-ignore-line
    }
}
