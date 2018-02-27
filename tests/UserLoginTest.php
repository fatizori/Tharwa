<?php
use \App\Models\User;

class UserLoginTest extends TestCase
{

    /**
     * Test if the used can correctly login.
     *
     * @return void
     * @throws Exception
     */
    public function testLogin()
    {
        //Create a new test user
        $user= factory(User::class,1)->create();

        //Login the test user
        $credentials=['email' => $user[0]->email,'password' => 'password'];
        $response=$this->json('POST', '/login',$credentials);

        //delete the test user
        $user[0]->delete();

        //Test
        $response->assertResponseStatus(200);
    }
}
