<?php

namespace App\Notifications;

use App\Mail\PasswordResetCodeMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordResetCodeNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly string $code)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Send the password reset code through the branded Mailable so the
     * email arrives with the Merlo Transportes design instead of the
     * generic Laravel markdown layout ("Regards, …"). The user object
     * travels with the Mailable so the view can greet them by name
     * and show the email the code was issued for.
     */
    public function toMail(object $notifiable): PasswordResetCodeMail
    {
        return (new PasswordResetCodeMail($notifiable, $this->code))->to($notifiable->email);
    }
}
