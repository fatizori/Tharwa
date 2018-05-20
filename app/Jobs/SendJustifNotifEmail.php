<?php

namespace App\Jobs;

use App\Mail\JustifNotifMail;
use Illuminate\Support\Facades\Mail;

class SendJustifNotifEmail extends Job
{
    private $email;
    private $account_id;
    private $action;

    /**
     * Create a new job instance.
     *
     * @param $email
     * @param $account_id
     * @param $action
     */
    public function __construct($email, $account_id, $action)
    {
        $this->email = $email;
        $this->account_id = $account_id;
        $this->action = $action;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        try {
            Mail::to($this->email)
                ->send(new JustifNotifMail($this->email, $this->account_id,$this->action));
            return true;
        } catch (\Exception $exception) {
            return false;
        }

    }
}
