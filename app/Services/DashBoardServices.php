<?php
namespace App\Services;


class DashBoardServices
{
    private $virementInternesService;
    private $virementExternesService;
    public function __construct()
    {
        $this->virementInternesService =new VirementInternesServices();
        $this->virementExternesService =new VirementExternesServices();
    }

    /**
     * @return bool
     */
    public function getTransferStat(){
        $internTransfers = $this->virementInternesService->getInternTransferStat();
        $externTransfers = $this->virementExternesService->getExternTransferStat();
        if (!is_null($internTransfers) && !is_null($externTransfers) ){
            $resulalt = [
                'trIntern' =>  $internTransfers,
                'trExtern' =>  $externTransfers
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