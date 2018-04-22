<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginServiceTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $LoginServices = null;
        parent::__construct($name, $data, $dataName);
        $LoginServices = new \App\Services\LoginServices();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $data ='';
        $this->checkUser($data);
        $this->assertTrue(true);
    }
}
