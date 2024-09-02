<?php

namespace App\Mail;

use App\Models\Subject_sub;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        
        $subject=Subject_sub::join('subject','subject.id','=','subject_sub.subject_id')
        ->whereIn('subject_sub.subject_id', explode(',', $this->data['subject_id']))
        ->where('subject_sub.institute_id', $this->data['institute_id'])
        ->get();
return $this->subject("Verification of Enrollment")
->view('emails.parentsverify')
->with([
        'data' => $this->data,
        'subjects' => $subject
    ]);
    }
}
