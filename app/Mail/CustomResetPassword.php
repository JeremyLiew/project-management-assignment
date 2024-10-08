<?php

namespace App\Mail;

/**
 * @author Soo Yu Hung
 */
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomResetPassword extends Mailable {

    use Queueable,
        SerializesModels;

    public $token;

    public function __construct($token) {
        $this->token = $token;
    }

    public function build() {
        return $this->subject('Reset Your Password')
                        ->view('emails.password-reset');
    }
}
