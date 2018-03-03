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
    public function testLogin()
    {
        //Create a new test user
        $user= factory(User::class,1)->create();

        //Login the test user
        $credentials=['email' => $user[0]->email,'password' => 'password'];
        $response=$this->json('POST', '/login',$credentials);

        //delete the test user
        $user[0]->delete();

        //Test Successful request
        $response->assertResponseStatus(200);
    }

    /**
     * Test if generating login with nonce is working .
     *
     * @return void
     * @throws Exception
     */
    public function testLoginNonce()
    {
        //Create a new test user
        $user= factory(User::class,1)->create();
        $nonce=$user[0]->nonce_auth;

        //Login the test user
        $credentials=['email' => $user[0]->email,'nonce' => $nonce];

        $response=$this->call('POST', '/login/code',[],[],[],$credentials,[]);

        //delete the test user
        $user[0]->delete();

        //Test Successful request
        $this->assertEquals(200,$response->status(),'Login failed');
    }

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

        //Login the test user
        $credentials = ['email' => $user[0]->email,'password' => 'password'];
        $response = $this->json('POST', '/login',$credentials);
        $token = $response->get('access_token');

        //Logout the test user
        $response = $this->call('POST', '/logout',[],[],[], ['Accept' => 'application/json',
        'Authorization' => 'Bearer '.$token->toString()],[]);

        //delete the test user
        $user[0]->delete();

        //Test Successful request
        $this->assertEquals(200,$response->status(),'Login failed');
    }
}