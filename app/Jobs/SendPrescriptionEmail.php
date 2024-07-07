<?php

namespace App\Jobs;

use App\Mail\PrescriptionMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPrescriptionEmail implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $patientName;
  protected $patientEmail;
  protected $doctorName;
  protected $fileName;
  protected $pdfContent; // Change to store the PDF content instead of file path

  /**
   * Create a new job instance.
   *
   * @param string $patientName
   * @param string $pdfContent
   */
  public function __construct($patientName, $pdfContent, $patientEmail, $doctorName, $fileName)
  {
    $this->patientName = $patientName;
    $this->pdfContent = $pdfContent;
    $this->patientEmail = $patientEmail;
    $this->doctorName = $doctorName;
    $this->fileName = $fileName;
  }

  /**
   * Execute the job.
   */
  public function handle()
  {
    $email = new PrescriptionMail($this->patientName, $this->pdfContent, $this->doctorName, $this->fileName);
    Mail::to($this->patientEmail)->send($email);
  }
}
