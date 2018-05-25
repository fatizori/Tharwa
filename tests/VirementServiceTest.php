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

    public function testGetInvalidVirementInternes(){
        DB::beginTransaction();
        $sender_account = \App\Models\Account::where('type',1)->first();
        $sender_account->balance =123.33;
        $receiver_account = \App\Models\Account::where('type',1)->orderBy('created_at', 'desc')->first();
        $virement = $this->virementService->createVirementBetweenCustomers($sender_account, $receiver_account, 123.33, 0, 0);
        // add an invalid justif
        $justif = \App\Models\JustificatifVirmInt::first();
        $justif->id_vrm = $virement->id;
        $justif->status = 0;
        $justif->save();
        // Get invalid virements
        $invalid_virements = $this->virementService->getInvalidVirementInternes();
        // Test that there is at least one invalid
        $this->assertNotEmpty($invalid_virements);
        // Test that all are invalid
        foreach ($invalid_virements as $invalid_virement){
               $virement = \App\Models\VirementInterne::find($invalid_virement->id_virement);
               self::assertEquals(0,$virement->status);
               // validate all virements
               $invalid_virement->status = 1;
               $invalid_virement->save();
               $justifs = \App\Models\JustificatifVirmInt::where('id_vrm',$virement->id)->get();
               foreach ($justifs as $justif){
                   $justif->status = 1;
                   $justif->save();
               }
        }

        // Test that there is at no one invalid
        // Get invalid virements
        $invalid_virements = $this->virementService->getInvalidVirementInternes();
        $this->assertEmpty($invalid_virements);

        // Set virement invalid. Test if there is an invalid account
        $justif->status = 0;
        $justif->save();
        $invalid_virements = $this->virementService->getInvalidVirementInternes();
        $this->assertNotEmpty($invalid_virements);


        // Set justif refused Test that virement is not invalid
        $this->virementService->refuseJustif($justif->id,4);
        $invalid_virements = $this->virementService->getInvalidVirementInternes();
        $this->assertEmpty($invalid_virements);

        DB::rollback();
    }


    public function testCreateVirementBetweenCustomers(){
        DB::beginTransaction();
        $sender_account = \App\Models\Account::where('type',1)->first();
        $sender_account->balance =123.33;
        $receiver_account = \App\Models\Account::where('type',1)->orderBy('created_at', 'desc')->first();
        $this->virementService->createVirementBetweenCustomers($sender_account, $receiver_account, 123.33, 0, 1);
        $this->seeInDatabase('virement_internes', [
            'montant_virement' => 123.33
        ]);
        DB::rollback();
    }

    //Test the service of creation of a virement betwwen the same user account
    public function testCreate(){
        DB::beginTransaction();

        //========================Test Virement current to Epargne==============================//

        //get the sender and the receiver account
        $sender_account =Account::where('type','=',1)
                         ->where('id_customer','=',6)
                         ->first();
        $receiver_account = Account::where('type','=',2)
                            ->where('id_customer','=',$sender_account->id_customer)
                            ->first();

        //the new balaance of the sender and receiver accounts
        $new_sender_balance = $sender_account->balance - 50 ;
        $new_receiver_balance = $receiver_account->balance + 50 ;


        $this->virementService->create('CVE',0,50,50,$sender_account,$receiver_account,0);

        $this->seeInDatabase('virement_internes', ['num_acc_sender' => $sender_account->id,'num_acc_receiver' => $receiver_account->id,
            'montant_virement'=>50,'code_curr_sender' => $sender_account->currency_code, 'code_curr_receiver' => $receiver_account->currency_code,
            'id_commission'=>'CVE','type'=>0,'montant_commission'=>0.00]);
        self::assertEquals(true,$sender_account->balance === $new_sender_balance);
        self::assertEquals(true,$receiver_account->balance === $new_receiver_balance );




        //================================= Epargne to Current =======================================//
        //get the sender and the receiver account
        $sender_account1 =Account::where('type','=',2)->first();
        $receiver_account1 = Account::where('type','=',1)
                            ->where('id_customer','=',$sender_account1->id_customer)
                            ->first();

        //the new balaance of the sender and receiver accounts
        $commission_amount = 0.10*50/100;
        $new_sender_balance1 = $sender_account1->balance - 50 - $commission_amount;
        $new_receiver_balance1 = $receiver_account1->balance + 50   ;


         $this->virementService->create('EVC',0,50,50,$sender_account1,$receiver_account1,0);

        $this->seeInDatabase('virement_internes', ['num_acc_sender' => $sender_account1->id,'num_acc_receiver' => $receiver_account1->id,
            'montant_virement'=>50,'code_curr_sender' => $sender_account1->currency_code, 'code_curr_receiver' => $receiver_account1->currency_code,
            'id_commission'=>'EVC','type'=>0,'montant_commission'=>$commission_amount]);

        //compare the sender and the receiver balance
        self::assertEquals(true,$sender_account1->balance === $new_sender_balance1);
        self::assertEquals(true,$receiver_account1->balance === $new_receiver_balance1 );

        //=================================  Current to Euro  =======================================//
        //get the sender and the receiver account
        $sender_account1 =Account::where('type','=',1)->first();
        $receiver_account1 = Account::where('type','=',3)
                            ->where('id_customer','=',$sender_account1->id_customer)
                            ->first();

        //the new balaance of the sender and receiver accounts
        $commission_amount = 2.00*50/100;
        $new_sender_balance1 = $sender_account1->balance - 50 - $commission_amount;
        $new_receiver_balance1 = $receiver_account1->balance + 20   ;


        $this->virementService->create('CVD',0,50,20,$sender_account1,$receiver_account1,0);

        $this->seeInDatabase('virement_internes', ['num_acc_sender' => $sender_account1->id,'num_acc_receiver' => $receiver_account1->id,
            'montant_virement'=>50,'code_curr_sender' => $sender_account1->currency_code, 'code_curr_receiver' => $receiver_account1->currency_code,
            'id_commission'=>'CVD','type'=>0,'montant_commission'=>$commission_amount]);

        //compare the sender and the receiver balance
        self::assertEquals(true,$sender_account1->balance === $new_sender_balance1);
        self::assertEquals(true,$receiver_account1->balance === $new_receiver_balance1 );

        //================================= Euro to Current   =======================================//
        //get the sender and the receiver account
        $sender_account1 =Account::where('type','=',3)->first();
        $receiver_account1 = Account::where('type','=',1)
                            ->where('id_customer','=',$sender_account1->id_customer)
                            ->first();

        //the new balaance of the sender and receiver accounts
        $commission_amount = 1.50*50/100;
        $new_sender_balance1 = $sender_account1->balance - 50 - $commission_amount;
        $new_receiver_balance1 = $receiver_account1->balance + 20   ;


        $this->virementService->create('DVC',0,50,20,$sender_account1,$receiver_account1,0);

        $this->seeInDatabase('virement_internes', ['num_acc_sender' => $sender_account1->id,'num_acc_receiver' => $receiver_account1->id,
            'montant_virement'=>50,'code_curr_sender' => $sender_account1->currency_code, 'code_curr_receiver' => $receiver_account1->currency_code,
            'id_commission'=>'DVC','type'=>0,'montant_commission'=>$commission_amount]);

        //compare the sender and the receiver balance
        self::assertEquals(true,$sender_account1->balance === $new_sender_balance1);
        self::assertEquals(true,$receiver_account1->balance === $new_receiver_balance1 );
        DB::rollback();


    }

}