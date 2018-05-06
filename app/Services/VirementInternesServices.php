<?php

namespace App\Services;
use App\Jobs\LogJob;
use App\Models\Account;
use App\Models\JustificatifVirmInt;
use App\Models\VirementInterne;
use App\Services\CommissionsServices;
use App\Services\AccountsServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use  App\Http\Controllers\FilesController;

class VirementInternesServices
{
    const PATH_JUSTIF = 'images/justifications/';
    const IMAGE_JUSTiF = 'images/justificatif_vrm/';
    const IMAGE_MIN = 'images/justificatif_vrm_min/';

    public function __construct()
    {
        $this->accountService = new AccountsServices();
    }
    /**
     * @param $data
     * @param $codeCommission
     * @param $typeCommission
     * @param $montant
     * @param $sender_id
     * @param $receiver_id
     */
    public function create($data,$codeCommission,$typeCommission,$amount,$sender_id,$receiver_id,$account_sender,$account_receiver){

        $virementInterne = new VirementInterne();
        $virementInterne->num_acc_sender = strip_tags($data['num_acc_sender']);
        $virementInterne->code_bnk_sender = 'THW';
        $virementInterne->code_curr_sender = strip_tags($data['code_curr_sender']);
        $virementInterne->num_acc_receiver = strip_tags($data['num_acc_receiver']);
        $virementInterne->code_bnk_receiver = 'THW';
        $virementInterne->code_curr_receiver = strip_tags($data['code_curr_receiver']);
        $virementInterne->montant_virement = $amount;
        $virementInterne->status = 1;
        $virementInterne->type = $data['type'];
        //find commission by code
        $commissionC = new CommissionsServices();
        $commission = $commissionC->findByid($codeCommission);
        $virementInterne->id_commission = $codeCommission;
        if($typeCommission == 0){
            $virementInterne->montant_commission = $commission->valeur * $data['montant_virement'];
        }else{
            $virementInterne->montant_commission = $commission->valeur;
        }
        $virementInterne->save();

        //update the  sender's account balance
        $senderBalance = $account_sender->balance - $data['montant_virement'];
        $this->accountService->updateAccountBalance($account_sender,$senderBalance);

        //update the receiver's account balance
        $receiverBalance = $amount - $virementInterne->montant_commission + $account_receiver->balance;
        $this->accountService->updateAccountBalance($account_receiver,$receiverBalance);

        //log
        dispatch(new LogJob($sender_id,$receiver_id,'Virement effectue',11, LogJob::SUCCESS_STATUS));

    }

    /**
     * @param $sender_account
     * @param $receiver_account
     * @param $montant
     * @param $type
     * @return VirementInterne
     */
    public function createBetweenCustomersExchange($sender_account, $receiver_account, $montant, $type){
            return $this->createVirementBetweenCustomers($sender_account,$receiver_account,$montant,$type,1);
    }

//    public function createBetweenCustomersExchangeJustif($sender_customer, $codeAccount, $montant, $type, $justif){
//        $sender_id = $sender_customer->id;
//        DB::beginTransaction();
//        $failureResponse =$this->checkVirementBeforeCreate($codeAccount,$sender_id,$montant);
//        if (!$failureResponse){
//            $this->createVirementBetweenCustomers($sender_account,$codeAccount,$montant,$type,0);
//            // log
//            dispatch(new LogJob($sender_id,$reciever_account->id_customer,'Virement effectué',11,
//                LogJob::SUCCESS_STATUS));
//
//            // Upload justif
//            $justif->move($destinationPathJustifs, $imagename);
//
//
//            DB::commit();
//            return response(json_encode(['message'=>'virement effectué']),201);
//        }
//        DB::rollback();
//        return $failureResponse;
//    }

    private function createVirementBetweenCustomers($sender_account, $reciever_account, $montant, $type, $status){
        $virementInterne = new VirementInterne();
        $virementInterne->num_acc_sender = strip_tags($sender_account->id);
        $virementInterne->code_bnk_sender = 'THW';
        $virementInterne->code_curr_sender = 'DZD';
        $virementInterne->num_acc_receiver = $reciever_account->id;
        $virementInterne->code_bnk_receiver ='THW';
        $virementInterne->code_curr_receiver = 'DZD';
        $virementInterne->montant_virement = $montant;
        $virementInterne->status = $status;
        $virementInterne->type = $type;
        //find commission by type
        $commissionC = new CommissionsServices();
        $commission = $commissionC->findById('VCT');
        $virementInterne->id_commission = 5;
        //Extract commission value
        $virementInterne->montant_commission = $commission->valeur/100 * $montant;
        $virementInterne->save();
        return $virementInterne;
    }




    public function addJustif($justification,$id_sender){
        $file = new FilesController;
        $picture_url = $file->uploadImage($justification,self::IMAGE_JUSTiF,self::IMAGE_MIN,$id_sender);
        $justificatif_vrm = new JustificatifVirmInt();
        $justificatif_vrm ->url_justif = $picture_url ;
        $justificatif_vrm ->status = 0;
        $justificatif_vrm ->save();
    }

   public function getInvalidVirementInternes(){
        $virement = VirementInterne::join('justificatif_virm_int', 'justificatif_virm_int.id_vrm', '=', 'virement_internes.id')
                    ->where('virement_internes.status' ,0 )
                    ->where('justificatif_virm_int.status' ,0 )
                    ->select('justificatif_virm_int.id','virement_internes.id','num_acc_sender','num_acc_receiver','virement_internes.code_bnk_receiver','virement_internes.created_at','url_justif')
                    ->get();
        return $virement;
   }
}
