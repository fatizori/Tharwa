<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 12-04-2018
 * Time: 11:42
 */

namespace App\Services;
use App\Jobs\LogJob;
use App\Models\Account;
use App\Models\JustificatifVirmInt;
use App\Models\VirementInterne;
use App\Services\CommissionsServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use  App\Http\Controllers\FilesController;

class VirementInternesServices
{
    const IMAGE_JUSTiF = 'images/justificatif_vrm/';
    const IMAGE_MIN = 'images/justificatif_vrm_min/';
    /**
     * @param $data
     * @param $codeBankSender
     * @param $codeBankReceiver
     * @param $codeCommission
     * @param $typeCommission
     * @param $montant
     */
    public function create($data,$codeCommission,$typeCommission,$montant,$sender_id,$receiver_id){


        $virementInterne = new VirementInterne();
        $virementInterne->num_acc_sender = strip_tags($data['num_acc_sender']);
        $virementInterne->code_bnk_sender = 'THW';
        $virementInterne->code_curr_sender = strip_tags($data['code_curr_sender']);
        $virementInterne->num_acc_receiver = strip_tags($data['num_acc_receiver']);
        $virementInterne->code_bnk_receiver = 'THW';
        $virementInterne->code_curr_receiver = strip_tags($data['code_curr_receiver']);
        $virementInterne->montant_virement = $montant;
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
        //log
        dispatch(new LogJob($sender_id,$receiver_id,'Virement effectue',11, LogJob::SUCCESS_STATUS));

    }

    /**
     * @param $sender_customer
     * @param $codeAccount
     * @param $montant
     * @param $type
     * @return bool
     */
    public function createBetweenCustomersExchange($sender_customer, $codeAccount, $montant, $type){
        $sender_id = $sender_customer->id;
        DB::beginTransaction();
        $reciever_account = Account::where('id',$codeAccount)
                                            ->where('type',0)
                                            ->first();
        if(is_null($reciever_account)){
            //log
            dispatch(new LogJob($sender_id,$codeAccount,'distnataire introuvable',11,
                LogJob::FAILED_STATUS));
            return response(json_encode(['message'=>'receiver not found']),404);
        }
        if($montant > 200000){
            //log
            dispatch(new LogJob($sender_id,$reciever_account->id_customer,'Virement de plus de 200000',11,
                LogJob::FAILED_STATUS));
            return response(json_encode(['message'=>'too big amount']),403);
        }
        $sender_account = Account::where('id_customer','=',$sender_id)->first();
           if($sender_account->balance < $montant ){
                //log
            dispatch(new LogJob($sender_id,$reciever_account->id_customer,'Virement avec montant insuffisant',11,
                LogJob::FAILED_STATUS));
            return response(json_encode(['message'=>'balance insuffisant']),400);
        }
        $virementInterne = new VirementInterne();
        $virementInterne->num_acc_sender = strip_tags($sender_account->id);
        $virementInterne->code_bnk_sender = 'THW';
        $virementInterne->code_curr_sender = 'DZD';
        $virementInterne->num_acc_receiver = $codeAccount;
        $virementInterne->code_bnk_receiver ='THW';
        $virementInterne->code_curr_receiver = 'DZD';
        $virementInterne->montant_virement = $montant;
        $virementInterne->status = 0;
        $virementInterne->type = $type;
        //find commission by type
        $commissionC = new CommissionsServices();
        $commission = $commissionC->findById('VCT');
        $virementInterne->id_commission = 5;
        //Extract commission value
        $virementInterne->montant_commission = $commission->valeur/100 * $montant;
        $virementInterne->save();
        //log
         dispatch(new LogJob($sender_id,$reciever_account->id_customer,'Virement effectué',11,
             LogJob::SUCCESS_STATUS));
        DB::commit();
            return response(json_encode(['message'=>'virement effectue']),201);
    }

    public function addJustif($justification,$id_sender){
        $file = new FilesController;
        $picture_url = $file->uploadImage($justification,self::IMAGE_JUSTiF,self::IMAGE_MIN,$id_sender);
        $justificatif_vrm = new JustificatifVirmInt();
        $justificatif_vrm ->url_justif = $picture_url ;
        $justificatif_vrm ->status = 0;
        $justificatif_vrm ->save();
    }
}
