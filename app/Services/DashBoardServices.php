<?php
namespace App\Services;


class DashBoardServices
{
    private $virementInternesService;
    private $virementExternesService;
    private $accountsService;
    public function __construct()
    {
        $this->virementInternesService = new VirementInternesServices();
        $this->virementExternesService = new VirementExternesServices();
        $this->accountsService = new AccountsServices();
    }

    /**
     * @return bool
     */
    public function getStat(){
        $internTransfers = $this->virementInternesService->getInternTransferStat();
        $externTransfers = $this->virementExternesService->getExternTransferStat();
        $accountStat = $this->accountsService->getAccountsStat();
        if (!is_null($internTransfers) && !is_null($externTransfers) ){
            $resulalt = [
                'trIntern' =>  $internTransfers,
                'trExtern' =>  $externTransfers,
                'accountManage' => $accountStat
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