<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 26-03-2018
 * Time: 21:49
 */

namespace App\Services;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\JustificatifAccount;
use Carbon\Carbon;


class JustificationServices
{

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findById($id){
        return JustificatifAccount::find($id);
    }

    /**
     *
     * @param $id_account
     * @param $justification
     * @return void
     */
    public function createAccountJustif($id_account,$justification){
            $justif = new JustificatifAccount();
            $justif->justification = $justification;
            $justif->id_account = $id_account;
            $justif->save();
    }

    /**
     * @param $id_account
     * @return JustificatifAccount
     */
    public function getLastJustifByAccountId($id_account){
        $justif = JustificatifAccount::where('id_account',$id_account)->get()->last();
        return $justif;
    }

    /**
     * Waiting justifs
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getInvalidatedJustif(){
        $justif = JustificatifAccount::where('status',0)
            ->get()->all();
        return $justif;
    }

    /**
     * Waiting justifs
     * @param $account_id
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getInvalidatedJustifByAccountId($account_id){
        $justif = JustificatifAccount::where('status',0)
            ->where('id_account',$account_id)->get()->last();
        return $justif;
    }

    /**
     * @param $action
     * @param $id_account
     */
    public function disactiverJustif($action , $id_account){
        JustificatifAccount::where('id_account',$id_account)
        ->update(['status'=>$action]);
               //action == 1 -> accepted | action == 2 -> refused
    }

}