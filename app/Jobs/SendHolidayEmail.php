<?php

namespace App\Jobs;

use App\Mail\HolidayMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendHolidayEmail implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   */
  protected $doctorName;
  protected $doctorEmail;
  protected $pdfContent;
  protected $fileName;
  public function __construct($doctorName, $doctorEmail, $pdfContent, $fileName)
  {
    $this->doctorName = $doctorName;
    $this->pdfContent = $pdfContent;
    $this->doctorEmail = $doctorEmail;
    $this->fileName = $fileName;
  }

  /**
   * Execute the job.
   */
  public function handle()
  {
    $email = new HolidayMail($this->doctorName, $this->doctorEmail, $this->pdfContent, $this->fileName);
    Mail::to($this->doctorEmail)->send($email);
  }
}
