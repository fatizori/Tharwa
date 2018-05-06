<?php

namespace App\Services;
use App\Jobs\LogJob;
use App\Models\JustificatifAccount;
use App\Models\JustificatifVirmInt;
use App\Models\VirementInterne;
use  App\Http\Controllers\FilesController;

class VirementInternesServices
{
    const IMAGE_JUSTiF = 'images/justificatif_vrm/';
    const IMAGE_MIN = 'images/justificatif_vrm_min/';

    /**
     * @param $data
     * @param $codeCommission
     * @param $typeCommission
     * @param $montant
     * @param $sender_id
     * @param $receiver_id
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
     * @param $sender_account
     * @param $receiver_account
     * @param $montant
     * @param $type
     * @return VirementInterne
     */
    public function createBetweenCustomersExchange($sender_account, $receiver_account, $montant, $type){
            return $this->createVirementBetweenCustomers($sender_account,$receiver_account,$montant,$type,1);
    }


    /**
     * @param $sender_account
     * @param $receiver_account
     * @param $montant
     * @param $type
     * @return VirementInterne
     */
    public function createBetweenCustomersExchangeJustif($sender_account, $receiver_account, $montant, $type){
        return $this->createVirementBetweenCustomers($sender_account,$receiver_account,$montant,$type,0);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|mixed|null|static
     */
    public function getTransfertById($id){
        $virement = VirementInterne::where('id',$id);
        return $virement;
    }



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


    /**
     * @param $id_justif
     * @return JustificatifAccount|JustificatifAccount[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
     public function getJustifById($id_justif)
     {
        $justif =  JustificatifVirmInt::where('id',$id_justif)->first();
        return $justif;
     }

}
