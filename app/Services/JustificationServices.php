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
     * Create Account
     * @param $id_banker
     * @param $id_account
     * @param $justification
     * @param $justifObject
     */
    public function createAccountJustif($id_banker,$id_account,$justification,$justifObject){
        $justif=new JustificatifAccount();
        $justif->object = $justifObject;
        $justif->justification = $justification;
        $justif->id_account = $id_account;
        $justif->id_banker = $id_banker;
        $justif->save();
    }

}