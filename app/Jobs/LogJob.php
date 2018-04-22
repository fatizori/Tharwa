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
    const FAILED_STATUS  = "failed";
    const SUCCESS_STATUS  = "success";
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
            case 0 :
                $this->type = 'login';
                break;
            case 1:
                $this->type = 'create';
                break;
            case 2 :
                $this->type = 'get data';
                break;
            case 3 :
                $this->type = 'update avatar';
                break;
            case 4 :
                $this->type = 'validate account';
                break;
            case 5 :
                $this->type = 'block account';
                break;
            case 6 :
                $this->type = 'unblock account';
                break;
            case 7 :
                $this->type = 'delete new account';
                break;
            case 8 :
                $this->type = 'update banker profile';
                break;
            case 9 :
                $this->type = 'block banker';
                break;
            case 10 :
                $this->type = 'user changed password';
                break;
            case 11 :
                $this->type = 'virement interne Tharwa';
                break;
            case 12 :
                $this->type = 'ajouter un compte';
                break;
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