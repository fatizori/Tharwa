<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 12-04-2018
 * Time: 8:48
 */

use App\Services\AccountsServices;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use \Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\VirementInterne;

class VirementServiceTest extends TestCase
{
   private $virementService;
    private $virementController;
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
       $this->virementService = new \App\Services\VirementInternesServices();
        $this->virementController = new \App\Http\Controllers\VirementInternesController();
    }

//
//    public  function  testCreate(){
//        DB::beginTransaction();
//
//        self::assertEquals(false,$result);
//
//        DB::rollback();
//    }
//
//    public function testGetInvalidVirementInternes(){
//        DB::beginTransaction();
//        $virement = $this->virementService->create();
//        $virements = $this->virementService->getInvalidVirementInternes();
//
//        DB::rollback();
//    }


    /*public function testCreateVirementBetweenCustomers(){
        DB::beginTransaction();
        $sender_account = \App\Models\Account::where('type',1)->first();
        $receiver_account = \App\Models\Account::where('type',1)->last();
        $virement = $this->createVirementBetweenCustomers($sender_account, $receiver_account, 200, 0, 1);
        $last_created_virement = \App\Models\VirementInterne::all()->get()->last();
        self::assertEquals(true,$virement === $last_created_virement);
        DB::rollback();
    }*/

    //Test the service of creation of a virement betwwen the same user account
    public function testCreate(){
        DB::beginTransaction();

        //========================Test Virement current to Epargne==============================//

        //get the sender and the receiver account
        $sender_account =Account::where('type','=',1)->first();
        $receiver_account = Account::where('type','=',2)->first();

        //the new balaance of the sender and receiver accounts
        $new_sender_balance = $sender_account->balance - 300;
        $new_receiver_balance = $receiver_account->balance + 300 - 0.00 ;


        $virement = $this->virementService->create(0,'CVE',0,300,$sender_account,$receiver_account);

        $this->seeInDatabase('virement_internes', ['num_acc_sender' => $sender_account->id,'num_acc_receiver' => $receiver_account->id,
            'montant_virement'=>300,'code_curr_sender' => $sender_account->currency_code, 'code_curr_receiver' => $receiver_account->currency_code,
            'id_commission'=>'CVE','type'=>0,'montant_commission'=>0.00]);
        self::assertEquals(true,$sender_account->balance === $new_sender_balance);
        self::assertEquals(true,$receiver_account->balance === $new_receiver_balance );
        DB::rollback();



        //================================= Epargne to Current =======================================//
        //get the sender and the receiver account
        $sender_account1 =Account::where('type','=',2)->first();
        $receiver_account1 = Account::where('type','=',1)->first();

        //the new balaance of the sender and receiver accounts
        $commission_amount = 0.10*50/100;
        $new_sender_balance1 = $sender_account1->balance - 50;
        $new_receiver_balance1 = $receiver_account1->balance + 50 - $commission_amount  ;


        $virement = $this->virementService->create(0,'EVC',0,50,$sender_account1,$receiver_account1);

        $this->seeInDatabase('virement_internes', ['num_acc_sender' => $sender_account1->id,'num_acc_receiver' => $receiver_account1->id,
            'montant_virement'=>50,'code_curr_sender' => $sender_account1->currency_code, 'code_curr_receiver' => $receiver_account1->currency_code,
            'id_commission'=>'EVC','type'=>0,'montant_commission'=>$commission_amount]);

        //compare the sender and the receiver balance
        self::assertEquals(true,$sender_account1->balance === $new_sender_balance1);
        self::assertEquals(true,$receiver_account1->balance === $new_receiver_balance1 );
        DB::rollback();


    }

}