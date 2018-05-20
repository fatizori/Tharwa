<?php
/**
 * Created by PhpStorm.
 * User: Mezerreg
 * Date: 14/05/2018
 * Time: 13:50
 */

namespace App\Services;
use App\Jobs\LogJob;
use App\Mail\JustifNotifMail;
use App\Mail\VirementNotifMail;
use App\Models\JustificatifAccount;
use App\Models\JustificatifVirmExt;
use App\Models\VirementExterne;
use  App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Mail;

class VirementExternesServices
{

    const IMAGE_JUSTiF = 'images/justificatif_vrm/';
    const IMAGE_MIN = 'images/justificatif_vrm_min/';

    /**
     * Get an externe transfer by id
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function getTransferById($id)
    {
        $virement = VirementExterne::where('id', $id)->first();
        return $virement;
    }

    public function getValideTransferByAccountId($id_account)
    {
        $virements = VirementExterne::where(function($q)use ($id_account){
            $q->where('num_acc',$id_account);
            $q->where('status',1);
        })->orWhere(function($q)use ($id_account){
            $q->where('num_acc_ext',$id_account);
            $q->where('status',1);
        })->simplePaginate(8)->setPath('');

        return $virements;
    }

    /**
     *  Find all invalid externe virement
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getInvalidVirementExternes()
    {
        $virement = VirementExterne::join('justificatif_virm_ext', 'justificatif_virm_ext.id_vrm', '=', 'virement_externes.id')

            ->where('virement_externes.status', 0)
            ->where('justificatif_virm_ext.status', 0)
            ->select('justificatif_virm_ext.id AS id_justif', 'virement_externes.id AS id_virement', 'num_acc', 'num_acc_ext', 'virement_externes.code_bnk_ext', 'virement_externes.created_at', 'virement_externes.amount_vir', 'url_justif')
            ->get();


        return $virement;
    }

    /**
     * Get all the externe virement
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getVirementExternes()
    {
        $virement = VirementExterne::select( 'id' , 'num_acc', 'num_acc_ext',
            'virement_externes.code_bnk_ext','virement_externes.created_at', 'virement_externes.amount_vir','status')
            ->get();


        return $virement;
    }

    /**
     * Create the externe virement
     * @param $sender_account
     * @param $data
     * @param $amount
     * @param $status
     * @param $sens
     * @return VirementExterne
     */
    public function createVirementExterne($local_account, $data, $amount,  $status , $sens)
    {
        $virementExterne = new VirementExterne();
        $virementExterne->num_acc = strip_tags($local_account->id);
        $virementExterne->code_bnk = 'THW';
        $virementExterne->code_curr = 'DZD';
        $virementExterne->num_acc_ext = $data['num_acc_ext'];
        $virementExterne->code_bnk_ext = $data['code_bnk_ext'];
        $virementExterne->code_curr_ext = $data['code_curr_ext'];
        $virementExterne->name_ext = $data['name'];
        $virementExterne->amount_vir = $amount;
        $virementExterne->status = $status;
        $virementExterne->sens = $sens;
        //find commission by type
        $commissionC = new CommissionsServices();
        if($sens == 0){
            $commission = $commissionC->findById('VCE');
            $virementExterne->id_commission = 'VCE';
        }else{
            $commission = $commissionC->findById('VRE');
            $virementExterne->id_commission = 'VRE';
        }
        //Extract commission value
        $virementExterne->amount_commission = $commission->valeur / 100 * $amount;
        $virementExterne->save();
        return $virementExterne;
    }

    /**
     *  Add the justification for the externe virement
     * @param $justification
     * @param $id_sender
     * @param $id_virement
     */
    public function addJustif($justification, $id_sender, $id_virement)
    {
        $file = new FilesController;
        $picture_url = $file->uploadImage($justification, self::IMAGE_JUSTiF, self::IMAGE_MIN, $id_sender);
        $justificatif_vrm = new JustificatifVirmExt();
        $justificatif_vrm->url_justif = $picture_url;
        $justificatif_vrm->id_vrm = $id_virement;
        $justificatif_vrm->status = 0;
        $justificatif_vrm->save();
    }

    /**
     * @param $local_account
     * @param $data
     * @param $amount
     * @param $sens
     * @return VirementExterne
     */
    public function createExterneExchange($local_account, $data, $amount,$sens)
    {
        return $this->createVirementExterne($local_account, $data, $amount, 1,$sens);
    }


    /**
     * @param $sender_account
     * @param $data
     * @param $amount
     * @return VirementExterne
     */
    public function createExterneExchangeJustif($sender_account, $data, $amount)
    {
        return $this->createVirementExterne($sender_account, $data, $amount, 0,0);
    }

    /**
     * Find a justification of externe transfer by id
     * @param $id_justif
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function getJustifById($id_justif)
    {
        $justif = JustificatifVirmExt::where('id', $id_justif)->first();
        return $justif;
    }


    /**
     * @param $justif_id
     * @param $banker_id
     * @return JustificatifAccount|JustificatifAccount[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function acceptJustif($justif_id, $banker_id)
    {
        $justif = JustificatifVirmExt::where('id', $justif_id)->first();
        $justif->status = 1;
        $justif->id_banker = $banker_id;
        $justif->save();
    }


    /**
     * @param $transfer
     */
    public function acceptTranfer($transfer)
    {
        $transfer->status = 1;
        $transfer->save();
    }

    /**
     * Refuse a justfication
     * @param $justif_id
     * @param $banker_id
     */
    public function refuseJustif($justif_id, $banker_id)
    {
        $justif = JustificatifVirmExt::where('id', $justif_id)->first();
        $justif->status = 2;
        $justif->id_banker = $banker_id;
        $justif->save();
    }

    /**
     * Refuse a transfer
     * @param $transfer
     * @return JustificatifAccount|JustificatifAccount[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function refuseTranfer($transfer)
    {
        $transfer->status = 2;
        $transfer->save();
    }

}