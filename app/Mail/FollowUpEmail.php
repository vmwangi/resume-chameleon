<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FollowUpEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('🌟 How’s Your Resume Chameleon Experience?')
                    ->view('emails.follow_up')
                    ->with([
                        'user' => $this->user,
                    ]);
    }
}