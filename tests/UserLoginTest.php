<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserLoginTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * Test if the used can correctly login.
     *
     * @return void
     */
    public function testLogin()
    {
        /* - Create user (register)

            $response = $this->call('POST', '/login',['username' => 'test@email.com',
                'password' => 'password'],[],[], ['Accept' => 'application/json'],[]);

            $this->assertEquals(200, $response->status(), 'Unexpected status');
        */
    }
}
