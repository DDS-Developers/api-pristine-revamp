<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BandungSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $submission;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($submission)
    {
        $this->submission = $submission;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mailUsername = env('MAIL_USERNAME');

        if (!filter_var($mailUsername, FILTER_VALIDATE_EMAIL)) {
            $mailUsername = 'customer@pristineofficial.com';
        }

        return $this->from($mailUsername)->view('mails.bandung_submission');
    }
}
