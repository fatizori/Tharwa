<?php
/**
 * Created by PhpStorm.
 * User: Fatizo
 * Date: 25/06/2018
 * Time: 13:00
 */
use App\Services\AccountsServices;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use \Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\VirementExterne;
class VirementExterneTest extends TestCase
{
    private $virementService;
    private $virementController;
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->virementService = new \App\Services\VirementExternesServices();
        $this->virementController = new \App\Http\Controllers\VirementExternesController();
    }

   public function testCreateVirementExterne(){

       DB::beginTransaction();
       //get the sender and the receiver account
       $sender_account =Account::where('type','=',1)
           ->where('id_customer','=',5)
           ->first();
       $receiver_account = ['num_acc_ext' =>12 ,'code_bnk_ext' =>'BDR','code_curr_ext' =>'DZD','name' =>'fatima mezerreg'];



       $this->virementService->createVirementExterne($sender_account, $receiver_account,300,1,0);

       $this->seeInDatabase('virement_externes', ['num_acc' => $sender_account->id,'num_acc_ext' => $receiver_account['num_acc_ext'],
           'amount_vir'=>300,'code_curr' => $sender_account->currency_code, 'code_curr_ext' => $receiver_account['code_curr_ext'],
           'id_commission'=>'VCE','amount_commission'=>6.00,'sens'=>0]);

       DB::rollback();
   }
}