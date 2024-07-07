<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class SendEmail extends Mailable
{
  use Queueable, SerializesModels;

  protected $token;
  protected $email;
  protected $url;

  /**
   * Create a new message instance.
   *
   * @param string $token
   * @param string $email
   */
  public function __construct($token, $email)
  {
    $this->token = $token;
    $this->email = $email;
    $this->url = URL::temporarySignedRoute('auth-reset-password-basic', now()->addHours(72), ['token' => $token, 'email' => $email]);
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->view('emails.send')
      ->subject('Mot de passe Metamed')
      ->with([
        'token' => $this->token,
        'email' => $this->email,
        'url' => $this->url
      ]);
  }
}
