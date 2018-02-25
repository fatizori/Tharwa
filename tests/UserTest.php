<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * Test if the admin can insert a new user.
     *
     * @return void
     */
    public function testInsertUser()
    {
        /*
        -create manager and get $acces_token
        $response = $this->call('POST', '/user',['username' => 'test@email.com',
            'password' => 'password'],[],[], ['Accept' => 'application/json',
            'Validation' => 'Bearer'.$acces_token],[]);
        $this->assertEquals(200, $response->status(), 'Unexpected status');
        */
    }
}
