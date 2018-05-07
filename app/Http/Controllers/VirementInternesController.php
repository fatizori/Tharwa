<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\VirementInternesServices;
use App\Services\AccountsServices;
use  App\Jobs\LogJob;
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


    /**
     * Get Invalid Virements Internes
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvalidVirementInternes(){
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
            dispatch(new LogJob('','',"Input validation error",11,LogJob::FAILED_STATUS));
            return   response()->json(['message' => $validator->errors()->all()], 400);
        }
        try{
            DB::beginTransaction();
        $currency = new CurrenciesController();
        $amount = $data['montant_virement'];

        //Get the id of the customer
            $id_customer = $request->user()->id;


        //sender and receiver account
        $account_sender = $this->accountService->findAccountByType($data['type_acc_sender'],$id_customer);
        $account_receiver = $this->accountService->findAccountByType($data['type_acc_receiver'],$id_customer);

        //if the receiver account dosen't exist
        if(is_null($account_receiver)){
            //log
            dispatch(new LogJob($account_sender->id_customer,$account_receiver->id,'receiver not found',11,
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


        // type currency dollar or euro
         if($data['type_acc_sender'] >= 3 || $data['type_acc_receiver'] >= 3 ){
             $amount = $currency->exchangeRate($amount,$account_sender->code_curr_sender,$account_receiver->code_curr_receiver);
         }

         //Find the commission code
        $codeCommission= $this->codeCommission($data['type_acc_sender'],$data['type_acc_receiver']);


        $this->virementInterneService->create($codeCommission,0,$amount,$account_sender,$account_receiver,$data['type']);



            DB::commit();
        return response(json_encode(['message' => 'transfer success']),201);
        } catch (\Exception $e) {
            DB::rollback();
            //log information
            //dispatch(new LogJob($account_sender->id_customer, $account_receiver->id_customer, $e->getMessage(), 11, LogJob::FAILED_STATUS));
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
            'type'=>'required | integer | between:0,1',
            'justif' => 'image|mimes:jpeg,png,jpg,bmp|max:2048'

        ];
        $data=$request->json()->all();
        $data['justif'] = $request->file('justif');
        $montant = $data['montant_virement'];

        $validator = Validator::make($data, $rules);
        if ($montant < 0 && !$validator->passes()) {
            return   response()->json(['message' => $validator->errors()->all()], 400);
        }
        if ($montant > 200000 && is_null($data['justif'])) {
            return   response()->json(['message' => 'Justification required'], 403);
        }
        //Run exchange
        $user = $request->user();

        //Check the receiver
        $account_receiver = $this->accountService->findById($data['num_acc_receiver']);
        if (is_null($account_receiver) || 1 != $account_receiver->type){
            dispatch(new LogJob($user->id,$data['num_acc_receiver'],'distnataire introuvable',11,
                LogJob::FAILED_STATUS));
            return response(json_encode(['message'=>'receiver account not found']),404);
        }

        DB::beginTransaction();
        // Get sender account
        $senderAccount = $this->accountService->findCurrentAccountByUserId($user->id);

        if ($montant <= 200000) {
            try {
                $virement = $this->virementInterneService->createBetweenCustomersExchange($senderAccount, $account_receiver, $montant, $data['type']);

                $new_sender_balance = $senderAccount->balance - $data['montant_virement'];
                if($new_sender_balance < 0){
                    //log
                    dispatch(new LogJob($user->id, $account_receiver->id_customer, 'Virement non effectué (montant insuffisant)', 11,
                        LogJob::FAILED_STATUS));
                    return response(json_encode(['message' => 'montant insuffisant']), 400);
                }

                $this->excuteInternalTransfer($senderAccount,$account_receiver,$virement->montant_virement,$virement->montant_commission);

            } catch (\Exception $exception) {
                //log
                DB::rollback();
                dispatch(new LogJob($user->id, $account_receiver->id_customer, 'Virement non effectué (erreur serveur)', 11,
                    LogJob::FAILED_STATUS));
                return response(json_encode(['message' => $exception->getMessage()]), 500);

            }
        } elseif ($montant > 200000) {
            try {
                //add exchange
                 $this->virementInterneService->createBetweenCustomersExchangeJustif($user, $data['num_acc_receiver'], $montant, $data['type']);
                //add justif
                $this->virementInterneService->addJustif($data['justif'],$user->id);
            } catch (\Exception $exception) {
                DB::rollback();
                dispatch(new LogJob($user->id, $account_receiver->id_customer, 'Virement non effectué (erreur serveur)', 11,
                    LogJob::FAILED_STATUS));
                return response(json_encode(['message' => $exception->getMessage()]), 500);
            }
        }
        //log
        dispatch(new LogJob($user->id,$account_receiver->id_customer,'Virement effectué',11,
            LogJob::SUCCESS_STATUS));
        DB::commit();
        return response(json_encode(['message'=>'virement effectué']),201);
    }



    public function validateTransfer(Request $request,$id_justif){
        //Validation of data
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
            return   response()->json(['message' => 'justif non trouvé'], 404);
        }
        $id_transfert = $justif->id_vrm;
        $transfert = $this->virementInterneService->getTransferById($id_transfert);
        if (is_null($transfert)){
            return   response()->json(['message' => 'virement non trouvé'], 404);
        }
        $banker_id = $request->user()->id;
        try {
            DB::beginTransaction();
            // Refuse justif
            if($operation == 2 && $justif->status == 0 && $transfert->status == 0){
                    $this->virementInterneService->refuseJustif($justif->id,$banker_id);
                    $this->virementInterneService->refuseTranfer($transfert);
                    DB::commit();
                return   response()->json(['message' => 'justificatif refusé'], 200);
            }else if ($operation == 1 && $justif->status == 0 && $transfert->status == 0){
                // Accept justif
                    $this->virementInterneService->acceptJustif($justif->id,$banker_id);
                    $this->virementInterneService->acceptTranfer($transfert);
                    // Excute transfert
                    $senderAccount = $this->accountService->findSenderCurrentAccountByTransfer($transfert);
                    $receiverAccount = $this->accountService->findReceiverCurrentAccountByTransfer($transfert);
                    $new_sender_balance = $senderAccount->balance - $transfert->montant_virement;
                    if($new_sender_balance < 0){
                        //log
                        DB::rollback();
                        dispatch(new LogJob($senderAccount->id_customer, $receiverAccount->id_customer, 'Virement non effectué (montant insuffisant)', 11,
                            LogJob::FAILED_STATUS));
                        return response(json_encode(['message' => 'montant insuffisant']), 206);
                    }
                    $this->excuteInternalTransfer($senderAccount,$receiverAccount,$transfert->montant_virement,$transfert->montant_commission);
                    DB::commit();

                    return   response()->json(['message' => 'justificatif accepté, virement validé'], 200);
            }else if ( 0 != $transfert->status){
                    // Virement is already valide
                    // Refuse justif
                    $this->virementInterneService->refuseJustif($justif->id,$banker_id);
                    DB::commit();
                return   response()->json(['message' => 'Virement est déja valide'], 422);
            }else if ( 0 != $justif->status){
                    // Justif is already valide
                    // Refuse justif
                    $this->virementInterneService->refuseJustif($justif->id,$banker_id);
                    DB::commit();
                return response()->json(['message' => 'Justif est déja valide'], 422);
            }
        }catch (\Exception $exception) {
                    DB::rollback();
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

}
