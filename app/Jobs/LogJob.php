<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 17-03-2018
 * Time: 10:12
 */

namespace App\Jobs;
use App\Models\Log;


class LogJob extends Job
{
    /**
     * email subject
     */
    private $email_sub;

    /**
     * email object
     */
    private $email_obj;

    /**
     * event description
     */
    private $message;

    /**
     *  event type
     */
    private $type;

    /**
     *  event status
     */
    private $status;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email_sub,$email_obj,$message,$type,$status)
    {
        $this->message = $message;
        $this->email_sub = $email_sub;
        $this->email_obj = $email_obj;
        $this->type = $type;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch($this->type){
            case '0' :
                $this->type = 'login';
                break;
            case '1':
                $this->type = 'create';
                break;
            case '2' :
                $this->type = 'get data';
            default : return  response()->json(['message' => 'invalid type'], 400);
        }

        $log = new Log();
        $log->email_sub = $this->email_sub;
        $log->email_obj = $this->email_obj;
        $log->message = $this->message;
        $log->status = $this->status;
        $log->type = $this->type;
        $log->save();
    }
}