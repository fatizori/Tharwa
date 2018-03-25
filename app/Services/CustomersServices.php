<?php
namespace App\Services;

use App\Models\Customer;

class CustomersServices
{

    public function getInitialInfos($client_id){
        $customer = Customer::find($client_id);
        $accounts = $customer->accounts()->get();
        $current_account=array();
        $accounts_types=array();
        foreach ($accounts as $account){
            array_push($accounts_types, $account->type);
            if(0 == $account->type){
                $current_account['account_code'] = $account->bank_code
                                                .sprintf('%06u',$account->id)
                                                .$account->currency_code;
                $current_account['balance'] = $account->balance;
            }
        }
            //TODO Delete this shit!!
        $data = [
            'name' => $customer->name,
            'photo' => 'customer1.jpg',
            //'photo' => $customer->photo,
            'type' => $customer->type,
            'account_types' => $accounts_types,
            'current_account' => $current_account
        ];
        return $data;
    }
}