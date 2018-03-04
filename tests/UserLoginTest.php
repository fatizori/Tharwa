<?php
use \App\Models\User;

class UserLoginTest extends TestCase
{
    /**
     * Test if generating nonce auth is working .
     *
     * @return void
     * @throws Exception
     */
   /* public function testLogin()
    {
        //Create a new test user
        $user= factory(User::class,1)->create();
        $user[0]->update(['email' => 'mahfoud10info@gmail.com' , 'phone_number' => '213697332045']);

        //Login the test user by mail
        $credentials=['email' => $user[0]->email,'password' => 'password', 'channel' => 0];
        $response1=$this->json('POST', '/login',$credentials);

        //Login the test user by sms
        $credentials['channel'] = 1;
        $response2=$this->json('POST', '/login',$credentials);


        //delete the test user
        $user[0]->delete();

        //Test Successful request
        $response1->assertResponseStatus(200);
        $response2->assertResponseStatus(200);
    }*/

//    /**
//     * Test if generating login with nonce is working .
//     *
//     * @return void
//     * @throws Exception
//     */
//    public function testLoginNonce()
//    {
//        //Create a new test user
//        $user= factory(User::class,1)->create();
//        $nonce=$user[0]->nonce_auth;
//
//        //Login the test user
//        $credentials=['email' => $user[0]->email, 'password' => 'password' , 'nonce' => $nonce];
//
//        $response=$this->json('POST', '/login/code',$credentials);
//
//        //delete the test user
//        $user[0]->delete();
//
//        $response->assertResponseStatus(200);
//    }

    /**
     * Test if the used can correctly logout.
     *
     * @return void
     * @throws Exception
     */
    public function testLogout()
    {
        //Create a new test user
        $user= factory(User::class,1)->create();
        $nonce=$user[0]->nonce_auth;

        //Login the test user
        $credentials = ['email' => $user[0]->email,'password' => 'password', 'nonce' => $nonce];
        $token = $this->json('POST', '/login/code',$credentials)
            ->get('access_token');

        //Logout the test user
        $response = $this->call('POST', '/logout',[],[],[], ['Accept' => 'application/json',
        'Authorization' => 'Bearer '.$token],[]);

        //delete the test user
        //$user[0]->delete();

        //Test Successful request
        $this->assertEquals(200,$response->status(),$token->toString());
    }
}