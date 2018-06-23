<?php
/**
 * Created by PhpStorm.
 * User: Fatizo
 * Date: 23/06/2018
 * Time: 19:29
 */

namespace App\Services;
use App\Http\Controllers\CurrenciesController;
use App\Models\Account;
use App\Models\Bank;
use App\Models\MensuelleCommission;

class MensuelleCommissionsServices
{

     public function executeMensuelleCommissions(){
         //get cmmissions from account
         $service_account = new AccountsServices();
         $list_account = Account::where('status','!=',0)
             ->where('status','!=',4)->get();
         $service_commission = new CommissionsServices();
         $amount_commission = 0.0;

         //for each account take a commission amount according to the account's type
         foreach ($list_account as $account){

             //if is a current account
             if($account->type == 1){
                 $commission_value = $service_commission->findById('CMC')->valeur;
                 $amount_commission = $amount_commission +  $commission_value;

                 //update the user balance
                 $service_account->updateAccountBalance($account,$account->balance - $commission_value) ;

             }else if ($account->type == 2){ // if is an epargne account

                 $commission_value = $service_commission->findById('CME')->valeur;
                 $amount_commission = $amount_commission +  $commission_value;
                 //update the user balance
                 $service_account->updateAccountBalance($account,$account->balance - $commission_value) ;

             }else if ($account->type == 3 || $account->type == 4){

                 $commission_value = $service_commission->findById('CMD')->valeur;
                 $currency = new CurrenciesController();
                 $amount_commission = $amount_commission +  $commission_value;

                 //update the user balance
                 $new_balance = $account->balance - $currency->exchangeRate($commission_value,$account->currency_code,'DZD');
                 $service_account->updateAccountBalance($account,$new_balance) ;
             }
         }

         //create the mensuelle commission
         $mensuelle_commision = new MensuelleCommission();
         $mensuelle_commision->amount = $amount_commission;
         $mensuelle_commision->save();

         //update bank balance
         $service_bank = new BanksServices();
         $bank = $service_bank->findById('THW');
         $service_bank->updateBalance($bank,$amount_commission);
     }
}