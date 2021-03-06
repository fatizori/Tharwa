<?php
namespace App\Services;

use App\Models\Customer;

class CustomersServices
{

    /**
     * @param $client_id
     * @return array
     */
    public function getInitialInfos($client_id){
        $customer = Customer::find($client_id);
        $accounts = $customer->accounts()->get();
        $current_account=array();
        $accounts_types=array();
        foreach ($accounts as $account){
            if(1 == $account->type) {
                $current_account['account_code'] = 'THW'
                    . sprintf('%06u', $account->id)
                    . $account->currency_code;
                $current_account['balance'] = $account->balance;
                $current_account['currency_code'] = $account->currency_code;
                $current_account['type'] = $account->type;
            }
            array_push($accounts_types, $account->type );

        }
        $data = [
            'name' => $customer->name,
            'photo' => $customer->photo,
            'type' => $customer->type,
            'accounts_types' => $accounts_types,
            'current_account' => $current_account
        ];
        return $data;
    }

    /**
     * Find the customer by id
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findById($id){
        $customer = Customer::find($id);
        return $customer;
    }

    /**
     * Create a new customer
     * @param $data
     * @param $user_id
     * @param $photo
     */
    public function create($data,$user_id,$photo){
        $customer  = new Customer();
        $customer->name = strip_tags($data['name']);
        $customer->address = strip_tags($data['address']);
        $customer->fonction = strip_tags($data['fonction']);
        $customer->wilaya = strip_tags($data['wilaya']);
        $customer->commune = strip_tags($data['commune']);
        $customer->type = $data['type'];
        $customer->photo=$photo;
        $customer->id = $user_id;
        $customer->save();
    }

    /**
     * Update the customer avatar
     * @param $id
     * @param $photo
     */
    public function updatePhoto($id,$photo){
        $this->findById($id)->update(['photo'=>$photo]);
    }
}