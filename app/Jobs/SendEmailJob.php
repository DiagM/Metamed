<?php

namespace App\Jobs;

use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $token;
  protected $email;

  /**
   * Create a new job instance.
   *
   * @param string $token
   * @param string $email
   */
  public function __construct($token, $email)
  {
    $this->token = $token;
    $this->email = $email;
  }

  /**
   * Execute the job.
   */
  public function handle()
  {
    $email = new SendEmail($this->token, $this->email);
    Mail::to($this->email)->send($email);
  }
}
