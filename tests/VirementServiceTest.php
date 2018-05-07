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
//    public function testGetInvalidVirementInternes(){
//        DB::beginTransaction();
//        $virement = $this->virementService->create();
//        $virements = $this->virementService->getInvalidVirementInternes();
//
//        DB::rollback();
//    }


    public function testCreateVirementBetweenCustomers(){
        DB::beginTransaction();
        $sender_account = \App\Models\Account::where('type',1)->first();
        $receiver_account = \App\Models\Account::where('type',1)->last();
        $virement = $this->createVirementBetweenCustomers($sender_account, $receiver_account, 200, 0, 1);
        $last_created_virement = \App\Models\VirementInterne::all()->get()->last();
        self::assertEquals(true,$virement === $last_created_virement);
        DB::rollback();
    }

}