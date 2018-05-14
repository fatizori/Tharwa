<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail;
use Illuminate\Queue\SerializesModels;

class VirementNotifMail extends Mail\Mailable
{
    use Queueable, SerializesModels;

    private $email_receiver;
    private $email_sender;
    public $subject='THARWA - Virements';

    /**
     * Create a new message instance.
     *
     * @param $email_receiver
     * @param $email_sender
     */
    public function __construct($email_receiver,$email_sender)
    {
        $this->email_receiver=$email_receiver;
        $this->email_sender=$email_sender;
    }

    /**
     * Build the message.
     *
     * @return VirementNotifMail
     */
    public function build()
    {
        return $this->view('virement_mail', ['email_receiver' => $this->email_receiver,'email_sender' => $this->email_sender],
            function ($message){
            $message->subject($this->subject)
                    ->from(config('mail.from.address'));
        });
    }
}
