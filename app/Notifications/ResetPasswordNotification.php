<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPasswordBase
{
    /**
     * Build the mail message.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable)
    {
        $url = url(config('app.frontend_url') . '/reset-password?token=' . $this->token . '&email=' . $notifiable->email);

       return (new MailMessage)
        ->subject('🔑 Reset Your EasyBondi Password')
        ->view('emails.password_reset', [
            'name' => $notifiable->first_name,
            'url' => $url,
        ]);

    }
}
