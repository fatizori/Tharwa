<?php

namespace App\Services;
use App\Jobs\LogJob;
use App\Jobs\SendJustifNotifEmail;
use App\Jobs\SendTransferNotifEmail;
use App\Jobs\SendTransferSameUserNotifEmail;
use App\Mail\VirementNotifMail;
use App\Models\JustificatifAccount;
use App\Models\JustificatifVirmInt;
use App\Models\VirementExterne;
use App\Models\VirementInterne;
use  App\Http\Controllers\FilesController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use phpDocumentor\Reflection\Types\Integer;

class VirementInternesServices
{
    const IMAGE_JUSTiF = 'images/justificatif_vrm/';
    const IMAGE_MIN = 'images/justificatif_vrm_min/';
    const TYPES_TRANS = ['VCT','CVE','CVD','DVC','EVC'];

    private $accountService;
    public function __construct()
    {
        $this->accountService = new AccountsServices();
    }

    /**
     * @param $codeCommission
     * @param $typeCommission
     * @param $amount
     * @param $new_amount
     * @param $sender
     * @param $receiver
     * @param $type
     */
    public function create( $codeCommission, $typeCommission, $amount, $new_amount, $sender, $receiver, $type)
    {
        $virementInterne = new VirementInterne();
        $virementInterne->num_acc_sender = $sender->id;
        $virementInterne->code_bnk_sender = 'THW';
        $virementInterne->code_curr_sender = $sender->currency_code;
        $virementInterne->num_acc_receiver = $receiver->id;
        $virementInterne->code_bnk_receiver = 'THW';
        $virementInterne->code_curr_receiver = $receiver->currency_code;
        $virementInterne->montant_virement = $amount;
        $virementInterne->status = 1;
        $virementInterne->type =$type;
        //find commission by code
        $commissionC = new CommissionsServices();
        $commission = $commissionC->findById($codeCommission);
        $virementInterne->id_commission = $codeCommission;
        if ($typeCommission == 0) {
            $virementInterne->montant_commission = $commission->valeur * $amount/100;
        } else {
            $virementInterne->montant_commission = $commission->valeur;
        }
        $virementInterne->save();

        //update the  sender's account balance
        $senderBalance = $sender->balance - $amount - $virementInterne->montant_commission ;
        $this->accountService->updateAccountBalance($sender, $senderBalance);

        //update the receiver's account balance
        $receiverBalance = $new_amount + $receiver->balance;
        $this->accountService->updateAccountBalance($receiver, $receiverBalance);

        //log
        dispatch(new LogJob($sender->id, $receiver->id, 'Virement effectue', 11, LogJob::SUCCESS_STATUS));

    }

    /**
     * @param $sender_account
     * @param $receiver_account
     * @param $montant
     * @param $type
     * @return VirementInterne
     */
    public function createBetweenCustomersExchange($sender_account, $receiver_account, $montant, $type)
    {
        return $this->createVirementBetweenCustomers($sender_account, $receiver_account, $montant, $type, 1);
    }


    /**
     * @param $sender_account
     * @param $receiver_account
     * @param $montant
     * @param $type
     * @return VirementInterne
     */
    public function createBetweenCustomersExchangeJustif($sender_account, $receiver_account, $montant, $type)
    {
        return $this->createVirementBetweenCustomers($sender_account, $receiver_account, $montant, $type, 0);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|mixed|null|static
     */
    public function getTransferById($id)
    {
        $virement = VirementInterne::where('id', $id)->first();
        return $virement;
    }


    /**
     * @param $id_account
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getValideTransferByAccountId($id_account)
    {
        $virements = VirementInterne::where(function($q)use ($id_account){
            $q->where('num_acc_sender',$id_account);
//            $q->where('status',1);            // Not just the valid ones
           })->orWhere(function($q)use ($id_account){
                $q->where('num_acc_receiver',$id_account);
                $q->where('status',1);
            })
            ->orderBy('created_at','DESC')
            ->simplePaginate(8)->setPath('');

        return $virements;
    }


    public function createVirementBetweenCustomers($sender_account, $reciever_account, $montant, $type, $status)
    {
        $virementInterne = new VirementInterne();
        $virementInterne->num_acc_sender = strip_tags($sender_account->id);
        $virementInterne->code_bnk_sender = 'THW';
        $virementInterne->code_curr_sender = 'DZD';
        $virementInterne->num_acc_receiver = $reciever_account->id;
        $virementInterne->code_bnk_receiver = 'THW';
        $virementInterne->code_curr_receiver = 'DZD';
        $virementInterne->montant_virement = $montant;
        $virementInterne->status = $status;
        $virementInterne->type = $type;
        //find commission by type
        $commissionC = new CommissionsServices();
        $commission = $commissionC->findById('VCT');
        $virementInterne->id_commission = 'VCT';
        //Extract commission value
        $virementInterne->montant_commission = $commission->valeur / 100 * $montant;
        $virementInterne->save();
        return $virementInterne;
    }


    public function addJustif($justification, $id_sender, $id_virement)
    {
        $file = new FilesController;
        $picture_url = $file->uploadImage($justification, self::IMAGE_JUSTiF, self::IMAGE_MIN, $id_sender);
        $justificatif_vrm = new JustificatifVirmInt();
        $justificatif_vrm->url_justif = $picture_url;
        $justificatif_vrm->id_vrm = $id_virement;
        $justificatif_vrm->status = 0;
        $justificatif_vrm->save();
    }

    public function getInvalidVirementInternes()
    {
        $virement = VirementInterne::join('justificatif_virm_int', 'justificatif_virm_int.id_vrm', '=', 'virement_internes.id')

            ->where('virement_internes.status', 0)
            ->where('justificatif_virm_int.status', 0)
            ->select('justificatif_virm_int.id AS id_justif', 'virement_internes.id AS id_virement', 'num_acc_sender', 'num_acc_receiver', 'virement_internes.code_bnk_receiver', 'virement_internes.created_at', 'virement_internes.montant_virement', 'url_justif')
            ->get();


        return $virement;
    }

    /**
     * @param $id_justif
     * @return JustificatifAccount|JustificatifAccount[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function getJustifById($id_justif)
    {
        $justif = JustificatifVirmInt::where('id', $id_justif)->first();
        return $justif;
    }


    /**
     * @param $justif_id
     * @param $banker_id
     * @return JustificatifAccount|JustificatifAccount[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function acceptJustif($justif_id, $banker_id)
    {
        $justif = JustificatifVirmInt::where('id', $justif_id)->first();
        $justif->status = 1;
        $justif->id_banker = $banker_id;
        $justif->save();
    }


    /**
     * @param $transfer
     * @return JustificatifAccount|JustificatifAccount[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function acceptTranfer($transfer)
    {
        $transfer->status = 1;
        $transfer->save();
    }

    /**
     * @param $justif_id
     * @param $banker_id
     * @return JustificatifAccount|JustificatifAccount[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function refuseJustif($justif_id, $banker_id)
    {
        $justif = JustificatifVirmInt::where('id', $justif_id)->first();
        $justif->status = 2;
        $justif->id_banker = $banker_id;
        $justif->save();
    }

    /**
     * @param $transfer
     * @return JustificatifAccount|JustificatifAccount[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function refuseTranfer($transfer)
    {
        $transfer->status = 2;
        $transfer->save();
    }


    /**
     * This added function for sending auth mail
     *
     * @param $email1
     * @param $email2
     * @return void
     */
    public function sendVirementNotifMAil($email1, $email2)
    {
       dispatch(new SendTransferNotifEmail($email1, $email2));
    }

    /**
     * This added function for sending auth mail
     *
     * @param $email_receiver
     * @param $account_sender
     * @param $account_receiver
     * @param $amount
     * @param $account_currency
     * @return void
     */
    public function sendVirementSameUserNotifMAil($email_receiver,$account_sender,$account_receiver,$amount,$account_currency)
    {
        dispatch(new SendTransferSameUserNotifEmail($email_receiver,$account_sender,$account_receiver,$amount,$account_currency));
    }



    /**
     * This added function for sending auth mail
     *
     * @param $email
     * @param $account_id
     * @param $action
     * @return void
     */
    public function sendJustifNotifMAil($email, $account_id, $action)
    {
        dispatch(new SendJustifNotifEmail($email, $account_id, $action));
    }

//-------------------------------------- intern trans stat ---------------------------------------//
    /**
     * @return array|null
     */
    public function getInternTransferStat(){
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
            $chartDatas = VirementInterne::select([
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
     * @return array|null
     */
    public function getTransferPerMonthOfType($type){
            $chartDatas = VirementInterne::select([
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
     * @return array|null
     */
     public function getTransferPerYearOfType($type,$nbYear){
            $chartDatas = VirementInterne::select([
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
                $chartDataByDay[$data['year']] = $data['count'];
            }

            $date = new Carbon;
            for($i = 0; $i < $nbYear; $i++) {
                $dateString = $date->year;
                if(!isset($chartDataByDay[ $dateString ])){
                    $chartDataByDay[$dateString] = 0;
                }
                $date->subYear();
            }
            return $chartDataByDay;
    }



}

