<?php
use \App\Models\User;
use \App\Models\Compte;
use \App\Models\Customer;
use \App\Models\Banquier;
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
        $path = base_path('public/test/test.png');
        $photo = new UploadedFile($path, 'test.png', 'image/png', filesize($path), UPLOAD_ERR_OK,true);

        $data = ['email' => 'test@gmail.com', 'password' => 'test','nom'=>'test',
            'adresse'=>'adrtest','telephone'=>'0558794512','fonction'=>'doctor','wilaya'=>'alger','commune'=>'kouba','type'=>'client','photo'=>$photo];

        $response = $this->json('POST', '/register_customer', $data);

        $this->seeInDatabase('users', ['email' => 'test@gmail.com']);
        $this->seeInDatabase('customers', ['nom'=>'test',
            'adresse'=>'adrtest','telephone'=>'0558794512','fonction'=>'doctor','wilaya'=>'alger','commune'=>'kouba','type'=>'client']);

          $response->assertResponseStatus(201);

    }

    /**
     * Test if the user/banker were been created .
     *
     * @return void
     */
    public function testRegisterBanker()
    {

        $response = $this->json('POST', '/registerBanker/31', ['email' => 'testB@gmail.com', 'password' => 'testB','nom'=>'testB',
            'prenom'=>'testBP']);

        $this->seeInDatabase('users', ['email' => 'testB@gmail.com']);
        $this->seeInDatabase('banquiers', ['nom'=>'testB', 'prenom'=>'testBP']);

        $response->assertResponseStatus(201);

    }
}
