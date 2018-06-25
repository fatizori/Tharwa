<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 26-03-2018
 * Time: 21:49
 */

namespace App\Services;
use App\Models\Account;
use App\Models\AccountAction;
use App\Models\Customer;
use App\Models\VirementInterne;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class AccountsServices
{
    /**
     * Find all accounts
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findAll(){
        $accounts = Account::where('status','!=',0)
        ->where('status','!=',4)->get();
        foreach ($accounts as $account){
            $account->setAttribute('code',$account->getCode());
        }
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
     *  Find an account by type and id customer
     * @param $type
     * @param $id_customer
     * @param $withCode
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function findAccountByType($type,$id_customer,$withCode){
        $account = Account::where('id_customer',$id_customer)
            ->where('type',$type)
            ->first();
        if($withCode){
            $account->setAttribute('account_code',$account->getCode());
        }
        return $account;
    }

    /**
     * Find  an account by id
     * @param $user_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findAccountsByUserId($user_id){
        $accounts = Customer::find($user_id)->accounts()->get()->all();
        return $accounts;
    }

    /**
     * @param $customer
     * @param $type
     * @return mixed
     */
    public function findAccountTypeByUserId($customer,$type){
        $account = $customer->accounts()->where('type','=',$type)->first();
        return $account;
    }


    /**
     * @param $transfer
     * @return mixed
     */
    public function findSenderCurrentAccountByTransfer($transfer)
    {
        return $transfer->account_sender()->first();
    }

    /**
     * @param $transfer
     * @return mixed
     */
    public function findReceiverCurrentAccountByTransfer($transfer)
    {
        return $transfer->account_receiver()->first();
    }



    /**
     * Find  an account by id
     * @param $user_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findCurrentAccountByUserId($user_id){
        $accounts = Customer::find($user_id)->accounts()->where('type',1)->first();
        return $accounts;
    }


    /**
     * Find  an account by id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findAllAccountsToBlock(){
        $accounts = Account::where('status',1)->orWhere('status',2)
        ->get()->all();
        return $accounts;
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
            ->select('accounts.id','name','fonction','address','phone_number','email','accounts.type','accounts.status')
            ->get();
        return $accounts;
    }


    /**
     * @param $account
     * @return bool
     */
    public function validateNewAccount($account){
        if(0 == $account->status){
            $account->update(['status'=> 1]);
            return true;
        }
        return false;
    }

    /**
     * @param $account
     * @return bool
     */
    public function unblockAccount($account){
        if(3 == $account->status){
            $account->update(['status'=> 2]);
            return true;
        }
        return false;
    }

    /**
     * @param $account
     * @return bool
     */
    public function blockAccount($account){
        if(1 == $account->status || 2 == $account->status ){
            $account->update(['status'=> 3]);
            return true;
        }
        return false;
    }

    /**
     * @param $account
     * @param $id_banker
     * @param $operation
     * @param $justif_obj
     * @param $justif
     */
    public function createBankerAction($account, $id_banker, $operation,$justif_obj,$justif){
        $account->bankers()->attach($id_banker, ['operation' => $operation,'object' => $justif_obj, 'justification' => $justif]);
    }


    /**
     *  Delete an account logically
     */
    public function refuseNewAccount($account){
        if(0 == $account->status ){
            $account->update(['status'=> 4]);
            return true;
        }
        return false;
    }

    /**
     * Delete an account physically
     * Update the account status
     * @param $account
     * @param $type
     */
    public function updateAccountStatus($account,$type){
        $account->update(['status'=> $type]);
    }

    /**
     * Update the account balance
     * @param $account
     * @param $amount
     */
    public function updateAccountBalance($account,$amount){
        $account->update(['balance'=> $amount]);
    }

    /**
     * Delete an account
     * @param $account
     */
    public function delete($account){
        $account->delete();
    }


    /**
     * @param $accountList
     * @param $account_type
     * @param $currency_code
     * @param $user_id
     * @return bool
     */
    public function addAccount($accountList, $account_type, $currency_code , $user_id)
    {
        // Test if there is another similar account type
        if(array_search($account_type, array_column($accountList, 'type'), true)){
           return false;
        }
        // Accounts constraints
        if(!( ($account_type == 2 && $currency_code == 'DZD') ||
              ($account_type == 3 && $currency_code == 'EUR') ||
              ($account_type == 4 && $currency_code == 'USD'))){
            return false;
        }
        $newAccount = new Account([
            'currency_code' => $currency_code,
            'balance' => 20000,
            'type' => $account_type,
            'id_customer' => $user_id
        ]);

       return  $newAccount->save();
    }


    /**
     * @param $id
     * @return mixed
     */
    public function getUsernameByIdAccount($id){
        $username  = Account::join('customers', 'customers.id', '=', 'accounts.id_customer')
            ->where('accounts.id','=',$id)
            ->where('accounts.type','=',1)
            ->select('name','wilaya','commune')
            ->first();
        return $username;
    }

    /**
     * @return array|null
     */
    public function getAccountsStat(){
        try{
            $data = array();
            $dataQ = array();
            $dataM = array();
            $dataY = array();
            // Per Quarter
            $dataQ['block'] = $this->getTransferPerQuarter(3);
            $dataQ['unblock'] = $this->getTransferPerQuarter(2);
            $dataQ['accepte'] = $this->getTransferPerQuarter(1);
            $dataQ['refuse'] = $this->getTransferPerQuarter(4);
            $data['quarter'] = $dataQ;

            // Per Mounth
            $dataM['block'] = $this->getTransferPerMonth(3);
            $dataM['unblock'] = $this->getTransferPerMonth(2);
            $dataM['accepte'] = $this->getTransferPerMonth(1);
            $dataM['refuse'] = $this->getTransferPerMonth(4);
            $data['month'] = $dataM;

            // Per Year
            $dataY['block'] = $this->getTransferPerYear(3,3);
            $dataY['unblock'] = $this->getTransferPerYear(2,3);
            $dataY['accepte'] = $this->getTransferPerYear(1,3);
            $dataY['refuse'] = $this->getTransferPerYear(4,3);
            $data['year'] = $dataY;

            return $data;
        }catch (\Exception $e){
            return null;
        }
    }

    /**
     * @param $type
     * @return array
     */
    public function getTransferPerQuarter($type){
        $chartDatas = AccountAction::select([
            DB::raw('QUARTER(created_at) AS quarter'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->where('operation','=',$type)
            ->whereBetween('created_at', [Carbon::now()->subQuarter(4), Carbon::now()])
            ->groupBy('quarter')
            ->orderBy('quarter', 'ASC')
            ->get()
            ->toArray();

        $chartDataByQuarter = array();
        foreach($chartDatas as $data) {
            $chartDataByQuarter[$data['quarter']] = $data['count'];
        }

        $date = new Carbon;
        for($i = 0; $i < 4; $i++) {
            $dateString = $date->quarter;
            if(!isset($chartDataByQuarter[ $dateString ])){
                $chartDataByQuarter[ $dateString ] = 0;
            }
            $date->subQuarter();
        }

        return $chartDataByQuarter;
    }

    /**
     * @param $type
     * @return array|null
     */
    public function getTransferPerMonth($type){
        $chartDatas =   AccountAction::select([
            DB::raw('MONTH(created_at) AS month'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->where('operation','=',$type)
            ->whereBetween('created_at', [Carbon::now()->subMonth(12), Carbon::now()])
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->toArray();
        $chartDataByM = array();
        foreach($chartDatas as $data) {
            $chartDataByM[$data['month']] = $data['count'];
        }

        $date = new Carbon;
        for($i = 0; $i < 12; $i++) {
            $dateString = $date->month;
            if(!isset($chartDataByM[ $dateString ])){
                $chartDataByM[$dateString] = 0;
            }
            $date->subMonth();
        }
        return $chartDataByM;
    }

    /**
     * @param $type
     * @param $nbYear
     * @return array|null
     */
    public function getTransferPerYear($type,$nbYear){
        $chartDatas = AccountAction::select([
            DB::raw('YEAR(created_at) AS year'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->where('operation','=',$type)
            ->whereBetween('created_at', [Carbon::now()->subYear($nbYear), Carbon::now()])
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->get()
            ->toArray();
        $chartDataByYear = array();
        foreach($chartDatas as $data) {
            $i=1;
            $chartDataByYear[$i] = $data['count'];
            $i++;
        }

        for($i = 1; $i < $nbYear+1; $i++) {
            if(!isset($chartDataByYear[ $i])){
                $chartDataByYear[$i] = 0;
            }
        }
        return $chartDataByYear;
    }


    /**
     * @param $account_id
     * @return array
     */
    public function getLastBlockingMotif($account_id){
        $motif =  AccountAction::where('account_id',$account_id)
            ->where('operation',3)->select(['object','justification'])
            ->get()->last()->toArray();
        return $motif;
    }

}