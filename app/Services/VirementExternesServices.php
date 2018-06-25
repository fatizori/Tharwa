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
use App\Models\VirementInterne;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class VirementExternesServices
{

    const IMAGE_JUSTiF = 'images/justificatif_vrm/';
    const IMAGE_MIN = 'images/justificatif_vrm_min/';
    const TYPES_TRANS = ['VCE','VCT'];

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
//            $q->where('status',1);
        })->orWhere(function($q)use ($id_account){
            $q->where('num_acc_ext',$id_account);
//            $q->where('status',1);
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
     * @param $local_account
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

    /**
     *
     */
    public function getExternTransferStat(){
        try{
            $data = array();
            $dataQ = array();
            $dataM = array();
            $dataY = array();
            // Per Quarter
            foreach (self::TYPES_TRANS as $type){
                $dataQ[$type] = $this->getTransferPerQuarterOfType($type);
            }
            $data['quarter'] = $dataQ;

            // Per Mounth
            foreach (self::TYPES_TRANS as $type){
                $dataM[$type] = $this->getTransferPerMonthOfType($type);
            }
            $data['month'] = $dataM;

            // Per Year
            foreach (self::TYPES_TRANS as $type){
                $dataY[$type] = $this->getTransferPerYearOfType($type,3);
            }
            $data['year'] = $dataY;

            return $data;
        }catch (\Exception $e){
            return null;
        }
    }

    public function getTransferPerQuarterOfType($type){
        $chartDatas = VirementExterne::select([
            DB::raw('QUARTER(created_at) AS quarter'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->where('status','=',1)            // valid transfers
            ->where('id_commission','=',$type)
            ->whereBetween('created_at', [Carbon::now()->subQuarter(4), Carbon::now()])
            ->groupBy('quarter')
            ->orderBy('quarter', 'ASC')
            ->get()
            ->toArray();

        $chartDataByDay = array();
        foreach($chartDatas as $data) {
            $chartDataByDay[$data['quarter']] = $data['count'];
        }

        $date = new Carbon;
        for($i = 0; $i < 4; $i++) {
            $dateString = $date->quarter;
            if(!isset($chartDataByDay[ $dateString ])){
                $chartDataByDay[ $dateString ] = 0;
            }
            $date->subQuarter();
        }

        return $chartDataByDay;
    }

    /**
     * @param $type
     * @return array
     */
    public function getTransferPerMonthOfType($type){
        $chartDatas = VirementExterne::select([
            DB::raw('MONTH(created_at) AS month'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->where('status','=',1)            // valid transfers
            ->where('id_commission','=',$type)
            ->whereBetween('created_at', [Carbon::now()->subMonth(5), Carbon::now()])
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->toArray();
        $chartDataByDay = array();
        foreach($chartDatas as $data) {
            $chartDataByDay[$data['month']] = $data['count'];
        }

        $date = new Carbon;
        for($i = 0; $i < 12; $i++) {
            $dateString = $date->month;
            if(!isset($chartDataByDay[ $dateString ])){
                $chartDataByDay[$dateString] = 0;
            }
            $date->subMonth();
        }
        return $chartDataByDay;
    }

    /**
     * @param $type
     * @param $nbYear
     * @return array
     */
    public function getTransferPerYearOfType($type, $nbYear){
        $chartDatas = VirementExterne::select([
            DB::raw('YEAR(created_at) AS year'),
            DB::raw('COUNT(id) AS count'),
        ])
            ->where('status','=',1)            // valid transfers
            ->where('id_commission','=',$type)
            ->whereBetween('created_at', [Carbon::now()->subYear($nbYear), Carbon::now()])
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->get()
            ->toArray();
        $chartDataByDay = array();
        foreach($chartDatas as $data) {
            $i=1;
            $chartDataByDay[$i] = $data['count'];
            $i++;
        }

        for($i = 1; $i < $nbYear+1; $i++) {
            if(!isset($chartDataByDay[ $i])){
                $chartDataByDay[$i] = 0;
            }
        }
        return $chartDataByDay;
    }

}