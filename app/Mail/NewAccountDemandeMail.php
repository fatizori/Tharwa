<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail;
use Illuminate\Queue\SerializesModels;

class NewAccountDemandeMail extends Mail\Mailable
{
    use Queueable, SerializesModels;

    private $email_banquier;
    private $email_client;

    public $subject='THARWA - Demandes de nouveaux comptes';

    /**
     * Create a new message instance.
     *
     * @param $email_banquier
     * @param $email_client
     */
    public function __construct($email_banquier,$email_client)
    {
        $this->email_banquier=$email_banquier;
        $this->email_client=$email_client;
    }

    /**
     * Build the message.
     *
     * @return NewAccountDemandeMail
     */
    public function build()
    {
        return $this->view('new_account_mail',
            ['email_banquier' => $this->email_banquier,'email_client' => $this->email_client],
            function ($message){
            $message->subject($this->subject)
                    ->from(config('mail.from.address'));
        });
    }
}
