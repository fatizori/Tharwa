<?php

namespace App\Jobs;

use App\Mail\VirementNotifMail;
use Illuminate\Support\Facades\Mail;

class SendTransferNotifEmail extends Job
{
    private $emailSender;
    private $emailReceiver;

    /**
     * Create a new job instance.
     *
     * @param $emailSender
     * @param $emailReceiver
     */
    public function __construct( $emailReceiver , $emailSender)
    {
        $this->emailSender = $emailSender;
        $this->emailReceiver = $emailReceiver;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        try {
            Mail::to($this->emailReceiver)
                ->send(new VirementNotifMail($this->emailReceiver, $this->emailSender));
            return true;
        } catch (\Exception $exception) {
            return false;
        }

    }
}
