<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class HospitalPasswordReset extends Notification implements ShouldQueue
{
  use Queueable;

  public $token;

  public function __construct($token)
  {
    $this->token = $token;
  }

  public function via($notifiable)
  {
    return ['mail'];
  }

  public function toMail($notifiable)
  {
    return (new MailMessage)
      ->subject('Password Reset Request')
      ->markdown('emails.hospital-password-reset', [
        'url' => URL::temporarySignedRoute(
          'auth-reset-password-basic',
          now()->addMinutes(60),
          ['token' => $this->token, 'email' => $notifiable->email]
        ),
      ]);
  }
}
