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

class AccountServiceTest extends TestCase
{
   private $virementService;
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
       $this->virementService = new \App\Services\VirementInternesServices();
    }


    public  function  testCreate(){
        DB::beginTransaction();

       // self::assertEquals(false,$result);

        DB::rollback();
    }

}