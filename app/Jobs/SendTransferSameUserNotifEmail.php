<?php

namespace App\Jobs;

use App\Mail\VirementSameUserNotifMail;
use Illuminate\Support\Facades\Mail;

class SendTransferSameUserNotifEmail extends Job
{
    private $email_receiver;
    private $account_sender;
    private $account_receiver;
    private $amount;
    private $account_currency;

    /**
     * Create a new job instance.
     *
     * @param $email_receiver
     * @param $account_sender
     * @param $account_receiver
     * @param $amount
     * @param $account_currency
     */
    public function __construct( $email_receiver,$account_sender,$account_receiver,$amount,$account_currency)
    {
        $this->email_receiver=$email_receiver;
        $this->account_sender=$account_sender;
        $this->account_receiver=$account_receiver;
        $this->amount=$amount;
        $this->account_currency=$account_currency;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        try {
            Mail::to($this->email_receiver)
                ->send(new VirementSameUserNotifMail($this->email_receiver, $this->account_sender,
                    $this->account_receiver,$this->amount,$this->account_currency));
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
