<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HolidayMail extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   */
  protected $doctorName;
  protected $doctorEmail;
  protected $fileName;
  protected $pdfContent;
  public function __construct($doctorName, $doctorEmail, $pdfContent, $fileName)
  {
    $this->doctorName = $doctorName;
    $this->pdfContent = $pdfContent;
    $this->doctorEmail = $doctorEmail;
    $this->fileName = $fileName;
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Holiday Mail',
    );
  }

  public function build()
  {
    return $this->view('emails.holiday')
      ->subject('Holiday confirmation')
      ->with([
        'doctorName' => $this->doctorName,
      ])
      ->attachData(base64_decode($this->pdfContent), "{$this->fileName}", [
        'mime' => 'application/pdf',
      ]);
  }
}
