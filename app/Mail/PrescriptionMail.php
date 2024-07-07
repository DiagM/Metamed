<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PrescriptionMail extends Mailable
{
  use Queueable, SerializesModels;

  protected $patientName;
  protected $patientEmail;
  protected $doctorName;
  protected $fileName;
  protected $pdfContent;

  /**
   * Create a new message instance.
   *
   * @param string $patientName
   * @param string $pdfContent
   */
  public function __construct($patientName, $pdfContent, $doctorName, $fileName)
  {
    $this->patientName = $patientName;
    $this->pdfContent = $pdfContent;
    $this->doctorName = $doctorName;
    $this->fileName = $fileName;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->view('emails.prescription')
      ->subject('Prescription')
      ->with([
        'patientName' => $this->patientName,
        'doctorName' => $this->doctorName,
      ])
      ->attachData(base64_decode($this->pdfContent), "{$this->fileName}", [
        'mime' => 'application/pdf',
      ]);
  }
}
