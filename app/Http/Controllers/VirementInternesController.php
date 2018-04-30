<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 12-04-2018
 * Time: 10:28
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\VirementInternesServices;
use App\Services\AccountsServices;
use  App\Jobs\LogJob;
use App\Http\Controllers\CurrenciesController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class VirementInternesController extends Controller
{

    private $virementInterneService;
    private $accountService;
    /**
     * VirementInternesController constructor.
     */
    public function __construct()
    {
        $this->virementInterneService = new VirementInternesServices();
        $this->accountService = new AccountsServices();
    }
    
    public function transferToAccount(Request $request){

        //Validation of data
        $rules = [
            'num_acc_sender' => 'required',
            'type_acc_sender' => 'required',
            'code_curr_sender' => 'required',
            'num_acc_receiver'=>'required',
            'type_acc_receiver'=>'required',
            'code_curr_receiver'=>'required',
            'montant_virement'=>'required',
            'type'=>'required | integer | between:0,1',
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            dispatch(new LogJob('','',"Input validation error",11,LogJob::FAILED_STATUS));
            return   response()->json(['message' => $validator->errors()->all()], 400);
        }
        try{
            DB::beginTransaction();
        $currency = new CurrenciesController();
        $amount = $data['montant_virement'];

        //sender and receiver account
        $account_sender = $this->accountService->findById($data['num_acc_sender']);
        $account_receiver = $this->accountService->findById($data['num_acc_receiver']);

        //if the receiver account dosen't exist
        if(is_null($account_receiver)){
            //log
            dispatch(new LogJob($account_sender->id_customer,$data['num_acc_receiver'],'receiver not found',11,
                LogJob::FAILED_STATUS));
            return response(json_encode(['message'=>'receiver not found']),404);
        }

        //if the amount is bigger than the sender balance
        if($amount> $account_sender->balance ) {
            //log
            dispatch(new LogJob($account_sender->id_customer,$account_receiver->id_customer,'Transfer with insuffsant balance',11,
                LogJob::FAILED_STATUS));
            return response(json_encode(['message' => 'balance insuffisant ']),400);
        }

         //if the amount is bigger than 200000
         if($amount > 200000){
            $ruleJust = [
                'justification' => 'required',
            ];
            $validator = Validator::make($data, $ruleJust);
            if (!$validator->passes()) {
                dispatch(new LogJob($account_sender->id_customer,$account_receiver->id_customer,"Input validation error: missing justification",11,LogJob::FAILED_STATUS));
                return   response()->json(['message' => $validator->errors()->all()], 400);
            }
            
            $this->virementInterneService->addJustif($data['justification'],$account_sender );
         }

        //type
         if($data['type_acc_sender']== 3 || $data['type_acc_receiver']== 3 ){
             $amount = $currency->exchangeRate($amount,$data['code_curr_sender'],$data['code_curr_receiver']);   
         }

         //commission code
        $codeCommission= '';
        if($data['type_acc_sender'] == 1 && $data['type_acc_receiver'] == 2){
             $codeCommission = 'CVE';
        }else if($data['type_acc_sender'] == 2 && $data['type_acc_receiver'] == 1){
            $codeCommission = 'EVC';
        }else if($data['type_acc_sender'] == 1 && $data['type_acc_receiver'] ==3){
            $codeCommission = 'CVD';
        }else if($data['type_acc_sender'] == 3 && $data['type_acc_receiver'] == 1){
            $codeCommission = 'DVC';
        }

        $this->virementInterneService->create($data,$codeCommission,0,$amount,$account_sender->id_customer,$account_receiver->id_customer);

        //update the  sender's account balance
        $senderBalance = $account_sender->balance - $data['montant_virement'];
        $this->accountService->updateAccountBalance($account_sender,$senderBalance);

        //update the receiver's account balance
        $receiverBalance = $amount + $account_receiver->balance;
        $this->accountService->updateAccountBalance($account_receiver,$receiverBalance);

            DB::commit();
        return response(json_encode(['message' => 'transfer success']),201);
        } catch (\Exception $e) {
            DB::rollback();
            //log information
            dispatch(new LogJob($account_sender->id_customer,$account_receiver->id_customer,$e->getMessage(),11,LogJob::FAILED_STATUS));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
