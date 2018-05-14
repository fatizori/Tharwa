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

    public function getInvalidVirementExternes()
    {
        $virement = VirementExterne::join('justificatif_virm_ext', 'justificatif_virm_ext.id_vrm', '=', 'virement_externes.id')

            ->where('virement_externes.status', 0)
            ->where('justificatif_virm_ext.status', 0)
            ->select('justificatif_virm_ext.id AS id_justif', 'virement_externes.id AS id_virement', 'num_acc', 'num_acc_ext', 'virement_externes.code_bnk_ext', 'virement_externes.created_at', 'virement_externes.amount_vir', 'url_justif')
            ->get();


        return $virement;
    }

    public function getVirementExternes()
    {
        $virement = VirementExterne::select( 'id' , 'num_acc', 'num_acc_ext',
            'virement_externes.code_bnk_ext','virement_externes.created_at', 'virement_externes.amount_vir','status')
            ->get();


        return $virement;
    }



}