<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail;
use Illuminate\Queue\SerializesModels;

class JustifNotifMail extends Mail\Mailable
{
    use Queueable, SerializesModels;

    private $email_receiver;
    private $codeAccount;
    private $action;
    public $subject='THARWA - Virements';

    /**
     * Create a new message instance.
     *
     * @param $email_receiver
     * @param $codeAccount
     * @param $action
     */
    public function __construct($email_receiver,$codeAccount,$action)
    {
        $this->email_receiver=$email_receiver;
        $this->codeAccount=$codeAccount;
        $this->action=$action;
    }

    /**
     * Build the message.
     *
     * @return JustifNotifMail
     */
    public function build()
    {
        return $this->view('justif_mail', ['email_receiver' => $this->email_receiver,'account' => $this->codeAccount,
            'action' => $this->action],
            function ($message){
            $message->subject($this->subject)
                    ->from(config('mail.from.address'));
        });
    }
}
