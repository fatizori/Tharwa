<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 17-03-2018
 * Time: 10:12
 */

namespace App\Jobs;
use App\Models\Log;
use Carbon\Carbon;



class ExcuteInTransfertsJob extends Job
{
    const max_amount_justif = 200000;
    const THARWA_CODE = 'THW';

    private $virementExterneService;
    private $accountService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->virementExterneService = new VirementExternesServices();
        $this->accountService = new AccountsServices();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->executeTransfer();
    }


    private function executeTransfer(){
        $files = glob('XML_file_in/*xml');
        $xml =null;
        if (is_array($files)) {
            foreach($files as $filename) {
                $xml_file = file_get_contents($filename, FILE_TEXT);
                $xml = json_decode(json_encode(simplexml_load_string($xml_file)));
                // Check if sended to tharwa
                $code_bnk = $xml->destinataire->banque;
                if($code_bnk != self::THARWA_CODE){
                    dispatch(new LogJob('', '' , 'destinataire non tharwa', 16,LogJob::FAILED_STATUS));
                    // Rename file (not execute it again)
                    rename($filename,$filename.'_');
                    continue;
                }
                $code_sender = $xml->titulaire->compte;
                $id_sender = substr( $code_sender, 3 ,6 ) - 0;
                // Check if the receiver exists
                $id_receiver = $xml->destinataire;
                $id_receiver =  substr( $id_receiver->compte, 3 ,6 ) - 0;
                $account_receiver = $this->accountService->findById($id_receiver);
                if(is_null($account_receiver)){
                    dispatch(new LogJob($code_sender, $id_receiver , 'client destinataire non trouvé', 16,LogJob::FAILED_STATUS));
                    // Rename file (not execute it again)
                    rename($filename,$filename.'_');
                    continue;
                }
                $amount =  $xml->montant;
                if(json_encode($amount) == '{}' || $amount < 0 ){
                    dispatch(new LogJob($code_sender, $id_receiver , 'montant non valide', 16,LogJob::FAILED_STATUS));
                    // Rename file (not execute it again)
                    rename($filename,$filename.'_');
                    continue;
                }
                // Create transfer
                $info['num_acc_ext'] = $id_sender;
                $info['code_bnk_ext'] =  $xml->titulaire->banque;
                $info['code_curr_ext'] = substr( $code_sender, 9 ,11 );
                $info['name'] = $xml->titulaire->nom;
                $this->virementExterneService->createExterneExchange($account_receiver,$info,$amount,1);
                // Excute virement
                //TODO commission from where??

                $new_receiver_balance = $account_receiver->balance + $amount;
                $this->accountService->updateAccountBalance($account_receiver, $new_receiver_balance);

                // Rename file (not execute it again)
                rename($filename,$filename.'_');
            }
        }
    }
}