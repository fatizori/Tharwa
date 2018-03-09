<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 09-03-2018
 * Time: 17:55
 */

class CurrencyTest  extends TestCase
{
    /**
     * Test if the customer can get the exchange rate
     *
     * @return void
     */
    public function testGetExchangeRate()
    {
        $response = $this->call('GET', '/currency', [],[],[]);
        $this->assertEquals(200,$response->status(),"unexpected status");

    }
}