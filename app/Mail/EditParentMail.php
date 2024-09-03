<?php

namespace App\Mail;

use App\Models\Subject_sub;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EditParentMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject("Verification of Enrollment")
        ->view('emails.ParentEditEmailVerification')
        ->with([
                'data' => $this->data,
            ]);
            }
}
