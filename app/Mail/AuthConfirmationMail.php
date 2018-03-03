<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail;
use Illuminate\Queue\SerializesModels;

class AuthConfirmationMail extends Mail\Mailable
{
    use Queueable, SerializesModels;

    private $email_to;
    private $auth_code;
    public $subject='THARWA - Mail de confirmation';

    /**
     * Create a new message instance.
     *
     * @param $email_to
     * @param $auth_code
     */
    public function __construct($email_to,$auth_code)
    {
        $this->email_to=$email_to;
        $this->auth_code=$auth_code;
    }

    /**
     * Build the message.
     *
     * @return AuthConfirmationMail
     */
    public function build()
    {
        return $this->view('auth_mail', ['email' => $this->email_to,'code' => $this->auth_code],
            function ($message){
            $message->subject($this->subject)
                    ->from(config('mail.from.address'));
        });
    }
}
