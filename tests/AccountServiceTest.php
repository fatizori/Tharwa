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
   private $accountService;
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
       $this->accountService = new AccountsServices();
    }


    public  function  testValidateNewAccount(){
        DB::beginTransaction();
        $customer = \App\Models\Customer::all()->first();

        //Create a new test user
        $newAccount = new \App\Models\Account();
        $newAccount->currency_code = 'DZD';
        $newAccount->status = 0;
        $newAccount->id_customer = $customer->id;
        $newAccount->save();
        //Not valid. test if it will be valid
        $result = $this->accountService->validateNewAccount($newAccount);
        self::assertEquals(true,$result);
        //valid. test if it will be not valid
        $result = $this->accountService->validateNewAccount($newAccount);
        self::assertEquals(false,$result);

        DB::rollback();
    }

    public  function  testUnblockAccount(){
        DB::beginTransaction();
        //Get an account
        $account = \App\Models\Account::all()->first();

        //Block account
        $account -> status = 3;

        //Blocked. test if it will non Blocked
        $result = $this->accountService->unblockAccount($account);
        self::assertEquals(true,$result);
        //Not Blocked. test if it will passe
        $result = $this->accountService->unblockAccount($account);
        self::assertEquals(false,$result);

        DB::rollback();
    }

    public function  testblockAccount(){
        DB::beginTransaction();
        //Get an account
        $account = \App\Models\Account::all()->first();

        //Block account
        $account -> status = 2;

        //Blocked. test if it will be non Blocked
        $result = $this->accountService->blockAccount($account);
        self::assertEquals(true,$result);
        //Not Blocked. test if it will passe
        $result = $this->accountService->blockAccount($account);
        self::assertEquals(false,$result);

        DB::rollback();
    }
    public function  testrefuseNewAccount(){
        DB::beginTransaction();
        //Get an account
        $account = \App\Models\Account::all()->first();

        //set account on waiting
        $account -> status = 0;

        //En attent. test if it will be deleted
        $result = $this->accountService->refuseNewAccount($account);
        self::assertEquals(true,$result);
        //Deleted. test if it will passe
        $result = $this->accountService->refuseNewAccount($account);
        self::assertEquals(false,$result);

        DB::rollback();
    }

    public  function  testAddAccount(){
        DB::beginTransaction();
        //create customer
          \App\Models\User::create([
         'email' => 'test@email.com',
         'password' => 'TestPassword@95',
         'phone_number' => '+213557894578',
         'nonce_auth' => '1200',
         'expire_date_nonce' => Carbon\Carbon::now()->addHour()
        ]);
        $user = \App\Models\User::all()->last();
       \App\Models\Customer::create([
         'id' => $user->id,
         'name' => 'testName',
         'address' => 'test',
         'function' => 'test',
         'wilaya' => 'test Alger',
         'commune' => 'test comm',
         'type' => 0
        ]);

        //Get accounts
        $accounts = $this->accountService->findAccountsByUserId($user->id)->toArray();

        //Test invalid currency
        $result = $this->accountService->addAccount($accounts, 2, 'EUR' , $user->id);
        self::assertEquals(false,$result);

        //Test invalid type
        $result = $this->accountService->addAccount($accounts, 10, 'EUR' , $user->id);
        self::assertEquals(false,$result);

        //Test valid type and currency
        $result = $this->accountService->addAccount($accounts, 2, 'DZD' , $user->id);
        self::assertEquals(true,$result);

        //Test valid but existing type and valid currency
        $result = $this->accountService->addAccount($accounts, 2, 'DZD' , $user->id);
        self::assertEquals(false,$result);

        //Test all other valid available types
        $result = $this->accountService->addAccount($accounts, 3, 'EUR' , $user->id);
        self::assertEquals(true,$result);

        //Test all other valid available types
        $result = $this->accountService->addAccount($accounts, 4, 'USD' , $user->id);
        self::assertEquals(true,$result);

        DB::rollback();
    }
}