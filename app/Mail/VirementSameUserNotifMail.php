<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail;
use Illuminate\Queue\SerializesModels;

class VirementSameUserNotifMail extends Mail\Mailable
{
    use Queueable, SerializesModels;

    private $email_receiver;
    private $account_sender;
    private $account_receiver;
    private $amount;
    private $account_currency;

    public $subject='THARWA - Virements';

    /**
     * Create a new message instance.
     *
     * @param $email_receiver
     * @param $account_sender
     * @param $account_receiver
     * @param $amount
     * @param $account_currency
     */
    public function __construct($email_receiver,$account_sender,$account_receiver,$amount,$account_currency)
    {
        $this->email_receiver=$email_receiver;
        $this->account_sender=$account_sender;
        $this->account_receiver=$account_receiver;
        $this->amount=$amount;
        $this->account_currency=$account_currency;
    }

    /**
     * Build the message.
     *
     * @return VirementSameUserNotifMail
     */
    public function build()
    {
        return $this->view('virement_mail',
            ['email_receiver' => $this->email_receiver,'account_sender' => $this->account_sender,
                'account_receiver' => $this->account_receiver, 'amount' => $this->amount,
                'account_currency' => $this->account_currency],
            function ($message){
            $message->subject($this->subject)
                    ->from(config('mail.from.address'));
        });
    }
}
