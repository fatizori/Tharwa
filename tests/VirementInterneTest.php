<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 07-05-2018
 * Time: 12:23
 */
use App\Services\AccountsServices;
use App\Services\VirementInternesServices;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use \Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\VirementInterne;
class VirementInterneTest
{
    public function testCreate(){
        DB::beginTransaction();

        //Test Virement current to Epargne

        //get the sender and the receiver account
        //$sender_account =Account::where('type','=',1)->first();
        //$receiver_account = Account::where('type','=',2)->first();

        //the new balaance of the sender and receiver accounts
        //$new_sender_balance = $sender_account->balance - 300;
        //$new_receiver_balance = $receiver_account->balance + 300 - 0.00 ;

        $response = $this->json('POST', '/virements_internes', ['type_acc_sender' => 1, 'type_acc_receiver' => 1,'montant_virement'=>50,'type'=>0]);
        $this->seeInDatabase('virement_internes', ['num_acc_sender' => $sender_account->id,'num_acc_receiver' => $receiver_account->id,
            'montant_virement'=>300,'code_curr_sender' => $sender_account->currency_code, 'code_curr_receiver' => $receiver_account->currency_code,
            'id_commission'=>'CVE','type'=>0,'montant_commission'=>0.00]);
        DB::rollback();

        self::assertEquals(true,$sender_account->balance === $new_sender_balance);
        self::assertEquals(true,$receiver_account->balance === $new_receiver_balance );





        $response->assertResponseStatus(201);
    }
}