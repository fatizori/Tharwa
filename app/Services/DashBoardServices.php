<?php
namespace App\Services;


class DashBoardServices
{
    private $virementInternesService;
    private $virementExternesService;
    private $accountsService;
    private $commissionsService;
    public function __construct()
    {
        $this->virementInternesService = new VirementInternesServices();
        $this->virementExternesService = new VirementExternesServices();
        $this->accountsService = new AccountsServices();
        $this->commissionsService = new CommissionsServices();
    }

    /**
     * @return bool
     */
    public function getStat(){
        $internTransfers = $this->virementInternesService->getInternTransferStat();
        $externTransfers = $this->virementExternesService->getExternTransferStat();
        $accountStat = $this->accountsService->getAccountsStat();
        $commissionStat = $this->commissionsService->getCommissionStat();
        if (!is_null($internTransfers) && !is_null($externTransfers) ){
            $resulalt = [
                'trIntern' =>  $internTransfers,
                'trExtern' =>  $externTransfers,
                'accountManage' => $accountStat,
                'commissions' => $commissionStat
            ];
            return $resulalt;
        }else{
            return false;
        }
    }
    

    /**
     *
     */
    public function getCommissiontStat(){

    }
}