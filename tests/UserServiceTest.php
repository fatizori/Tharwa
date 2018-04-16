<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 12-04-2018
 * Time: 8:48
 */

class UserServiceTest  extends TestCase
{
    public function testCreate()
    {

        $response = $this->call('POST', '/create', ['email' => 'testB@gmail.com', 'password' => 'testB','phone_number'=>'+213557854578','role'=>0]);

         $this->seeInDatabase('users', ['email' => 'testB@gmail.com']);
        // $this->seeInDatabase('bankers', ['name'=>'testB', 'firstname'=>'testBP','address'=>'baraki']);

       // $response->assertResponseStatus(201);

    }

}