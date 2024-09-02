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
        if(!empty($this->data['subject_id']) && $this->data['institute_id']){
            $subject=Subject_sub::join('subject','subject.id','=','subject_sub.subject_id')
            ->whereIn('subject_sub.subject_id', explode(',', $this->data['subject_id']))
            ->where('subject_sub.institute_id', $this->data['institute_id'])
            ->get();
        }else
        {
         $subject="";        }
        
        return $this->subject("Verification of Enrollment")
        ->view('emails.parentsverify')
        ->with([
                'data' => $this->data,
                'subjects' => $subject
            ]);
            }
}
