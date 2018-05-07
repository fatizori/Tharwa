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

}