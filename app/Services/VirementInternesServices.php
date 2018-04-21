<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 12-04-2018
 * Time: 11:42
 */

namespace App\Services;
use App\Models\VirementInterne;
use App\Services\CommissionsServices;

class VirementInternesServices
{
    public function create($data,$codeBankSender,$codeBankReceiver,$codeCommission,$typeCommission,$montant){

        $virementInterne = new VirementInterne();
        $virementInterne->num_acc_sender = strip_tags($data['num_acc_sender']);
        $virementInterne->code_bnk_sender = $codeBankSender;
        $virementInterne->code_curr_sender = strip_tags($data['code_curr_sender']);
        $virementInterne->num_acc_receiver = strip_tags($data['num_acc_receiver']);
        $virementInterne->code_bnk_receiver = $codeBankReceiver;
        $virementInterne->code_curr_receiver = strip_tags($data['code_curr_receiver']);
        $virementInterne->montant_virement = $montant;
        $virementInterne->status = 0;
        $virementInterne->type = $data['type'];
        //find commission by code
        $commissionC = new CommissionsServices();
        $commission = $commissionC->findCommissionByCode($codeCommission);
        $virementInterne->id_commission = $commission->id;
        if($typeCommission == 0){
            $virementInterne->montant_commission = $commission->valeur * $data['montant_virement'];
        }else{
            $virementInterne->montant_commission = $commission->valeur;
        }
        $virementInterne->save();



    }



}
