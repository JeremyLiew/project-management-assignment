<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Mail;

/**
 * Description of VerificationEmail
 *
 * @author garys
 */
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable {

    use Queueable,
        SerializesModels;

    protected $user;

    public function __construct($user) {
        $this->user = $user;
    }

    public function build() {
        return $this->view('emails.verification')
                        ->with([
                            'name' => $this->user->name,
                            'verificationUrl' => url('/verify-email/' . $this->user->email_verification_token)
        ]);
    }
}
