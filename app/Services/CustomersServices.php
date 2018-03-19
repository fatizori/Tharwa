<?php
namespace App\Services;
use App\Models\Customer;

class CustomersServices
{

    public function getInitInfo($client_id){
        $customer = Customer::find($client_id);
        $data= [
    ];
    }
}