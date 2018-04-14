<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 26-03-2018
 * Time: 21:49
 */

namespace App\Services;
use App\Models\Account;
use App\Models\Customer;
use Carbon\Carbon;


class AccountsServices
{
    /**
     * Find all accounts
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findAll(){
        $accounts = Account::all();
        return $accounts;
    }

    /**
     * Find  an account by id
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findById($id){
        $account = Account::find($id);

        return $account;
    }



    /**
     * Create Account
     * @param $id
     * @param $type
     */
    public function create($id,$type){
        $account=new Account();
        $account->id_customer=$id;
        $account->type = $type;
        $account->save();
    }

    /**
     * Get invalid accounts
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getInvalidAccounts(){
        $accounts  = Account::join('customers', 'customers.id', '=', 'accounts.id_customer')
            ->join('users','users.id','=','accounts.id_customer')
            ->where('accounts.status','=',0)
            ->select('accounts.id','name','function','address','phone_number','email','accounts.type','accounts.status')
            ->get();
        return $accounts;
    }

    /**
     * Update the account status
     * @param $account
     * @param $type
     */
    public function updateAccountStatus($account,$type){
        $account->update(['status'=> $type]);
    }

    /**
     * Delete an account
     * @param $account
     */
    public function delete($account){
        $account->delete();
    }

}