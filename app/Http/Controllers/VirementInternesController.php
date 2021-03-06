<?php

namespace App\Http\Controllers;
use App\Models\Account;
use App\Models\Customer;
use App\Models\User;
use App\Services\CustomersServices;
use Illuminate\Http\Request;
use App\Services\VirementInternesServices;
use App\Services\VirementExternesServices;
use App\Services\AccountsServices;
use  App\Jobs\LogJob;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class VirementInternesController extends Controller
{

    const max_amount_justif = 200000;

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


    /**
     * Get Invalid Virements Internes
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvalidVirement(){
        $virementInvalid = $this->virementInterneService->getInvalidVirementInternes();
        return response()->json($virementInvalid, 200);
    }

    /**
     * Get the codeCommission
     * @param $senderAcountType
     * @param $receiverAccountType
     * @return string
     */
     public function codeCommission($senderAcountType,$receiverAccountType){
         $codeCommission= '';
         if($senderAcountType == 1 && $receiverAccountType == 2){
             $codeCommission = 'CVE';
         }else if($senderAcountType == 2 && $receiverAccountType == 1){
             $codeCommission = 'EVC';
         }else if($senderAcountType == 1 && $receiverAccountType ==3){
             $codeCommission = 'CVD';
         }else if($senderAcountType == 3 && $receiverAccountType == 1){
             $codeCommission = 'DVC';
         }else if($senderAcountType == 1 && $receiverAccountType ==4){
             $codeCommission = 'CVD';
         }else if($senderAcountType == 4 && $receiverAccountType == 1){
             $codeCommission = 'DVC';
         }

         return $codeCommission;
     }


    /**
     *  Transfer between a client's account
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function transferToAccount(Request $request){

        //Validation of data
        $rules = [
                    'type_acc_sender' => 'required',
                    'type_acc_receiver'=>'required',
                    'montant_virement'=>'required',
                    'type'=>'required | integer | between:0,1'
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            dispatch(new LogJob('','', 'Input validation error',11,LogJob::FAILED_STATUS));
            return   response()->json(['message' => $validator->errors()->all()], 400);
        }
        try{
            DB::beginTransaction();
            $currency = new CurrenciesController();
            $amount = $data['montant_virement'];
            $new_amount = $amount;

             //Get the id of the customer
             $id_customer = $request->user()->id;


                 //sender and receiver account
            $account_sender = $this->accountService->findAccountByType($data['type_acc_sender'],$id_customer,0);
            $account_receiver = $this->accountService->findAccountByType($data['type_acc_receiver'],$id_customer,0);
                //if the receiver account dosen't exist
            if(is_null($account_receiver)){
                //log
                dispatch(new LogJob($account_sender->id_customer,null,'receiver not found',11,
                    LogJob::FAILED_STATUS));
                return response(json_encode(['message'=>'receiver not found']),404);
            }
        //if the receiver account != current account
         if($data['type_acc_receiver'] == $data['type_acc_sender']) {
             //log
             dispatch(new LogJob($account_sender->id_customer, $account_receiver->id_customer, 'Transfer to the same account', 11,
                 LogJob::FAILED_STATUS));
             return response(json_encode(['message' => 'Virement vers le meme compte ']), 400);
         }
             //if the amount is bigger than the sender balance
             if ($amount > $account_sender->balance) {
                 //log
                 dispatch(new LogJob($account_sender->id_customer, $account_receiver->id_customer, 'Transfer with insuffsant balance', 11,
                     LogJob::FAILED_STATUS));
                 return response(json_encode(['message' => 'balance insuffisant ']), 400);
             }


            // type currency dollar or euro
                if($data['type_acc_sender'] >= 3 || $data['type_acc_receiver'] >= 3 ){

                     $new_amount = $currency->exchangeRate($amount,$account_sender->currency_code,$account_receiver->currency_code);
                }

             //Find the commission code
                $codeCommission = $this->codeCommission($data['type_acc_sender'], $data['type_acc_receiver']);


                 $this->virementInterneService->create($codeCommission, 0, $amount,$new_amount, $account_sender, $account_receiver, $data['type']);


                 DB::commit();
                 $newBalance = $account_receiver->balance;
            // Send mail
                $customer = $request->user();
                $this->virementInterneService->sendVirementSameUserNotifMAil($customer->email,
                $account_sender->getCode(),$account_receiver->getCode(),
                $new_amount,$account_receiver->code_curr_receiver);

            return response(json_encode(['message' => 'transfer success', 'balance' => $newBalance]),201);
        } catch (\Exception $e) {
            DB::rollback();
            //log information
            dispatch(new LogJob($account_sender->id_customer, $account_receiver->id_customer, $e->getMessage(), 11, LogJob::FAILED_STATUS));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }



    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function transferToOtherUser(Request $request){
        //Validation of data
        $rules = [
            'num_acc_receiver'=>'required | integer',
            'montant_virement'=>'required | numeric',
            'type'=>'required | integer | between:0,1'

        ];
        $data=$request->json()->all();
        $user = $request->user();
        // TODO
        if(emptyArray($data)){
            $data = $request->input();
        }
        //return   response()->json(['data' => $data], 200);
        $data['justif'] = $request->file('justif');
        if(!is_null($data['justif'])){
            $rules['justif'] = 'image|mimes:jpeg,png,jpg,bmp|max:2048';
        }
        $montant = $data['montant_virement'];

        $validator = Validator::make($data, $rules);
        if ($montant < 0 || !$validator->passes()) {
            return   response()->json(['message' => $validator->errors()->all()], 400);
        }
        if ($montant > self::max_amount_justif && is_null($data['justif'])) {
            return   response()->json(['message' => 'Justification required'], 403);
        }


        //Check the receiver
        $account_receiver = $this->accountService->findById($data['num_acc_receiver']);
        if (is_null($account_receiver) || 1 != $account_receiver->type){
            dispatch(new LogJob($user->id,$data['num_acc_receiver'],'distnataire introuvable',13,
                LogJob::FAILED_STATUS));
            return response(json_encode(['message'=>'receiver account not found']),404);
        }

        DB::beginTransaction();
        // Get sender account
        $senderAccount = $this->accountService->findCurrentAccountByUserId($user->id);

        if ($montant <= self::max_amount_justif) {
            try {
                $virement = $this->virementInterneService->createBetweenCustomersExchange($senderAccount, $account_receiver, $montant, $data['type']);

                $new_sender_balance = $senderAccount->balance - $data['montant_virement'];
                if($new_sender_balance < 0){
                    //log
                    dispatch(new LogJob($user->id, $account_receiver->id_customer, 'Virement non effectu?? (montant insuffisant)', 13,
                        LogJob::FAILED_STATUS));
                    return response(json_encode(['message' => 'montant insuffisant']), 422);
                }

                $this->excuteInternalTransfer($senderAccount,$account_receiver,$virement->montant_virement,$virement->montant_commission);

                // Send email to receiver
                $email_s = $user->email;
                $receiver = User::find($account_receiver->id_customer);
                $this->virementInterneService->sendVirementNotifMAil($receiver->email,$email_s);

                // Send email to sender
                $this->virementInterneService->sendJustifNotifMAil($user->email,$account_receiver->id,'accept??');

            } catch (\Exception $exception) {
                //log
                DB::rollback();
                dispatch(new LogJob($user->id, $account_receiver->id_customer, 'Virement non effectu?? (erreur serveur)', 13,
                    LogJob::FAILED_STATUS));
                return response(json_encode(['message' => $exception->getMessage()]), 500);

            }
        } elseif ($montant > self::max_amount_justif) {
            try {
                //add exchange
                 $account_receiver = $this->accountService->findById($data['num_acc_receiver']);
                 $virement = $this->virementInterneService->createBetweenCustomersExchangeJustif($senderAccount, $account_receiver, $montant, $data['type']);
                 // Execute transfer
                 $operation = $this->excuteInternalTransferJustif($senderAccount,$virement->montant_virement,$virement->montant_commission);
                if (!$operation){
                 //log
                    DB::rollback();
                    dispatch(new LogJob($senderAccount->id_customer, $account_receiver->id_customer, 'Virement non effectu?? (montant insuffisant)', 15,
                        LogJob::FAILED_STATUS));
                    return response(json_encode(['message' => 'montant insuffisant']), 206);
                    }
                 //add justif
                $this->virementInterneService->addJustif($data['justif'],$user->id,$virement->id);
            } catch (\Exception $exception) {
                DB::rollback();
                dispatch(new LogJob($user->id, $account_receiver->id_customer, 'Virement non effectu?? (erreur serveur)', 13,
                    LogJob::FAILED_STATUS));
                return response(json_encode(['message' => $exception->getMessage()]), 500);
            }
        }
        //log
        dispatch(new LogJob($user->id,$account_receiver->id_customer,'Virement effectu??',13,
            LogJob::SUCCESS_STATUS));
        DB::commit();
        return response(json_encode(['message'=>'virement effectu??']),201);
    }


    /**
     * @param Request $request
     * @param $id_justif
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateTransfer(Request $request,$id_justif){
        //Validation of data
        $user = $request->user();
        $rules = [
            'operation'=>'required | integer | between:1,2'
        ];
        $data= $request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            return   response()->json(['message' => $validator->errors()->all()], 400);
        }
        $operation = $data['operation'];
        $justif = $this->virementInterneService->getJustifById($id_justif);
        if (is_null($justif)){
            dispatch(new LogJob($user->email, null, 'justif inexistant', 15,LogJob::FAILED_STATUS));
            return   response()->json(['message' => 'justif non trouv??'], 404);
        }
        $id_transfert = $justif->id_vrm;
        $transfert = $this->virementInterneService->getTransferById($id_transfert);
        if (is_null($transfert)){
            dispatch(new LogJob($user->email, $id_transfert, 'justif inexistant', 15,LogJob::FAILED_STATUS));
            return   response()->json(['message' => 'virement non trouv??'], 404);
        }
        $banker_id = $request->user()->id;

        $senderAccount = $this->accountService->findSenderCurrentAccountByTransfer($transfert);
        $receiverAccount = $this->accountService->findReceiverCurrentAccountByTransfer($transfert);
        // Envoyer email de notification
        $sender = User::find($senderAccount->id_customer);
        $receiver = User::find($receiverAccount->id_customer);

        try {
            DB::beginTransaction();
            // Refuse justif
            if($operation == 2 && $justif->status == 0 && $transfert->status == 0){
                    $this->virementInterneService->refuseJustif($justif->id,$banker_id);
                    $this->virementInterneService->refuseTranfer($transfert);
                    // revoke amount to sender account
                    $this->revokeInternalTransferJustif($senderAccount,$transfert);
                    dispatch(new LogJob($user->email, $id_transfert, 'virement refus??', 15,LogJob::SUCCESS_STATUS));
                    // Send email to sender
                    $this->virementInterneService->sendJustifNotifMAil($sender->email,$receiverAccount->id,'refus??');
                    DB::commit();
                    return   response()->json(['message' => 'justificatif refus??'], 200);
            }else if ($operation == 1 && $justif->status == 0 && $transfert->status == 0){
                // Accept justif
                    $this->virementInterneService->acceptJustif($justif->id,$banker_id);
                    $this->virementInterneService->acceptTranfer($transfert);
                    $this->finalizeInternalTransferJustif($receiverAccount,$transfert);
                    dispatch(new LogJob($user->email, $id_transfert, 'virement valid??', 14,LogJob::SUCCESS_STATUS));

                    $this->virementInterneService->sendVirementNotifMAil($receiver->email,$sender->email);
                    // Send email to sender
                    $this->virementInterneService->sendJustifNotifMAil($sender->email,$receiverAccount->id,'accept??');
                    DB::commit();
                    return   response()->json(['message' => 'justificatif accept??, virement valid??'], 200);
            }else if ( 0 != $transfert->status){
                    // Virement is already valide
                    // Refuse justif
                    $this->virementInterneService->refuseJustif($justif->id,$banker_id);
                    // revoke amount to sender account
                    $this->revokeInternalTransferJustif($senderAccount,$transfert);
                    dispatch(new LogJob($user->email, $id_transfert, 'virement refus??', 15,LogJob::SUCCESS_STATUS));
                    DB::commit();
                return   response()->json(['message' => 'Virement est d??ja valide'], 422);
            }else if ( 0 != $justif->status){
                    // Justif is already valide
                    // Refuse justif
                    $this->virementInterneService->refuseJustif($justif->id,$banker_id);
                    // revoke amount to sender account
                    $this->revokeInternalTransferJustif($senderAccount,$transfert);
                    dispatch(new LogJob($user->email, $id_transfert, 'virement refus??', 15,LogJob::SUCCESS_STATUS));
                    DB::commit();
                return response()->json(['message' => 'Justif est d??ja valide'], 422);
            }
        }catch (\Exception $exception) {
                    DB::rollback();
                    dispatch(new LogJob($user->email, $id_transfert, 'virement non trait?? (erreur serveur)', 14,LogJob::FAILED_STATUS));
                    return response()->json(['message' => $exception->getMessage()], 500);
        }

    }

    /**
     * @param $senderAccount
     * @param $receiver_account
     * @param $amount
     * @param $commission
     */
    public function excuteInternalTransfer($senderAccount, $receiver_account, $amount, $commission){
        //update the  sender's account balance
        $new_sender_balance = $senderAccount->balance - $amount - $commission;
        $this->accountService->updateAccountBalance($senderAccount, $new_sender_balance);

        //update the  receiver's account balance
        $new_receiver_balance = $receiver_account->balance + $amount;
        $this->accountService->updateAccountBalance($receiver_account, $new_receiver_balance);
    }

    /**
     * @param $senderAccount
     * @param $amount
     * @param $commission
     * @return bool
     */
    public function excuteInternalTransferJustif($senderAccount, $amount, $commission){
        //update the  sender's account balance
        $new_sender_balance = $senderAccount->balance - $amount - $commission;
        if($new_sender_balance < 0) {
            return false;
        }
        $this->accountService->updateAccountBalance($senderAccount, $new_sender_balance);
        return true;
    }

    /**
     * @param $senderAccount
     * @param $transfer
     */
    public function revokeInternalTransferJustif($senderAccount, $transfer){
        //update the  sender's account balance
        $new_sender_balance = $senderAccount->balance + $transfer->montant_virement;
        $this->accountService->updateAccountBalance($senderAccount, $new_sender_balance);
    }

    /**
     * @param $receiver_account
     * @param $transfer
     */
    public function finalizeInternalTransferJustif($receiver_account,$transfer){
        //update the  receiver's account balance
        $new_receiver_balance = $receiver_account->balance + $transfer->montant_virement;
        $this->accountService->updateAccountBalance($receiver_account, $new_receiver_balance);
    }

}
