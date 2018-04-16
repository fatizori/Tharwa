<?php
use \App\Models\User;
use \App\Models\Account;
use \App\Models\Customer;
use \App\Models\Banker;
use Laravel\Lumen\Testing\DatabaseTransactions;
use \Illuminate\Http\UploadedFile;



class UserRegisterTest extends TestCase
{

    use DatabaseTransactions;
    /**
     * Test if the user/customer/account were been created .
     *
     * @return void
     */
    public function testRegisterCustomer()
    {

       /* $path = base_path('public/test/test.png');
        $photo = new UploadedFile($path, 'test.png', 'image/png', null, null,true);*/

        $data = ['email' => 'test@gmail.com', 'password' => 'test','name'=>'test','phone_number'=>'+213557854578',
            'address'=>'adrtest','function'=>'doctor','wilaya'=>'alger','commune'=>'kouba','type'=>'client'];
        $response = $this->call('POST', '/customers', $data,[],[]);

        //$this->seeInDatabase('users', ['email' => 'test@gmail.com']);
       // $this->seeInDatabase('customers', ['name'=>'test',
            //'address'=>'adrtest','phone_number'=>'0558794512','function'=>'doctor','wilaya'=>'alger','commune'=>'kouba','type'=>'client']);

          //$response->assertResponseStatus(201);
        $this->assertEquals(201,$response->status(),"unexpected status");

    }



    /**
     * Test if the user/banker were been created .
     *
     * @return void
     */
    public function testRegisterBanker()
    {

        $response = $this->json('POST', '/bankers/1', ['email' => 'testB@gmail.com', 'password' => 'testB','phone_number'=>'+213557854578','name'=>'testB',
            'firstname'=>'testBP','address'=>'baraki']);

        $this->seeInDatabase('users', ['email' => 'testB@gmail.com']);
        $this->seeInDatabase('bankers', ['name'=>'testB', 'firstname'=>'testBP','address'=>'baraki']);

        $response->assertResponseStatus(201);

    }
}
