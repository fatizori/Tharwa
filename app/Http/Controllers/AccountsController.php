<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\JustificatifAccount;
use App\Models\User;
use App\Models\VirementInterne;
use App\Services\AccountsServices;
use  App\Jobs\LogJob;
use App\Services\CustomersServices;
use App\Services\JustificationServices;
use App\Services\VirementExternesServices;
use App\Services\VirementInternesServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class AccountsController extends Controller
{
    private $accountsService;

    public function __construct()
    {
        $this->accountsService = new AccountsServices();
    }

    /**
     * Get all accounts
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        //get the list of accounts
        $accounts = $this->accountsService->findAll();
        $customerServices = new CustomersServices();
        foreach ($accounts as $account){
            $customer = $customerServices->findById($account->id_customer);
            $account->setAttribute('nom',$customer->name);
            $account->setAttribute('code',$account->getCode());
        }
        //if no account exists in the database
        if(!$accounts){
            return response()->json(['message' => 'No account was found'], 404);
        }
        return response()->json($accounts, 200);
    }

    /**
     * Get non validated accounts
     * @return \Illuminate\Http\JsonResponse
     */
    public function invalidAccounts(){
        //get the list of accounts non valide
        $accounts = $this->accountsService->getInvalidAccounts();
        //if no account exists in the database
        if(!$accounts){
            return response()->json(['message' => 'No account was found'], 404);
        }

        return response()->json($accounts, 200);
    }


    /**
     * get an account by id
     * @param Request $request
     * @param $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $type){
        $user_id = $request->user()->id;
        $account = $this->accountsService->findAccountByType($type, $user_id,1);
        if(!$account){
            return response()->json(['message' => "The account with {$type} doesn't exist"], 404);
        }
        return response()->json($account, 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNameAccount(Request $request, $id){
        $account = $this->accountsService->findById($id);
        if(is_null($account)){
            return response()->json(['message' => 'not found account'], 404);
        }
        $customer = Customer::find($account->id_customer);
        $data['name'] = $customer->name;
        $data['commune'] = $customer->commune;
        $data['wilaya'] = $customer->wilaya;
        return response()->json($data, 200);
    }

    /**
     * Delete an account
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        //find the account
        $account = $this->accountsService->findById($id);
        if(!$account){
            return response()->json(['message' => "The account with {$id} doesn't exist"], 404);
        }
        // delete the account
        $this->accountsService->delete($account);

        return response()->json(['message' =>"The acount with  id {$id} has been deleted"], 200);
    }

    /**
     * Validate the customer account
     * @param Request $request
     * @param $id_account
     * @return \Illuminate\Http\JsonResponse
     */
    public function  actionOnAccount(Request $request,$id_account){
        //Validation of data
        $rules = [
            'type' => 'numeric',
            'justif_object' => 'max:190',
            'justif' => 'max:255'
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            dispatch(new LogJob('','', 'Input validation error',11,LogJob::FAILED_STATUS));
            return   response()->json(['message' => $validator->errors()->all()], 400);
        }

        $banker = $request->user();
        //Get the email of the banker
        $email_banker = $banker->email;
        $type = $data['type'];
        //find the account
        $account = $this->accountsService->findById($id_account);

        if(!is_null($account)){
            switch($type){
                case 1:{
                    $message = 'new account was validated';
                    // validate a new account
                    $action = $this->accountsService->validateNewAccount($account);
                    // Save Banker Operation
                    $this->accountsService->createBankerAction($account, $banker->id, 1,'','');
                }break;
                case 2:{
                    $message = 'account was unblocked';
                    // unblock an account
                    $action = $this->accountsService->unblockAccount($account);
                    if ($action) {
                        // Update Justif status
                        $justifServices = new JustificationServices();
                        $justifServices->disactiverJustif(1, $account->id);
                        // Save Banker Operation
                        $this->accountsService->createBankerAction($account, $banker->id, 2, '', '');
                    }
                }break;
                case 3:
                    {
                        $message = 'account was blocked';
                        // block an account
                        $justif = $data['justif'];
                        $justif_object = $data['justif_object'];
                        if (is_null($justif) || !isset($justif)) {
                            dispatch(new LogJob('', '', 'Justif manque', 11, LogJob::FAILED_STATUS));
                            return response()->json(['message' => $validator->errors()->all()], 400);
                        }
                        $action = $this->accountsService->blockAccount($account);
                        if ($action){
                            // Save Banker Operation
                            $this->accountsService->createBankerAction($account, $banker->id, 3,$justif_object,$justif);
                        }
                    }break;
                case 4:{
                    $message = 'new account was refused';
                    // delete new account
                    $action = $this->accountsService->refuseNewAccount($account);
                    if ($action){
                        // Save Banker Operation
                        $this->accountsService->createBankerAction($account, $banker->id, 4,'','');
                    }
                }break;
                default:{
                    return  response()->json(['message' => 'invalid operation'], 400);
                }
            }
            // log information
            if (! $action){
                // if the operation not accepted
                dispatch(new LogJob($email_banker,$id_account,'unaccepted operation',$type+4,LogJob::FAILED_STATUS));
                return  response()->json(['message' => 'unaccepted operation'], 400);
            }
            dispatch(new LogJob($email_banker,$id_account,$message,$type+4,LogJob::SUCCESS_STATUS));
            return  response()->json(['message' => 'status account has been updated successfully '], 200);
        }else{
            dispatch(new LogJob($email_banker,$id_account,'account not found',$type+4,LogJob::FAILED_STATUS));
            return response()->json(['message' => 'account not found'], 404);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addNewLocalAccount(Request $request){
        $rules = [
            'account_type' => 'required | integer | between:2,4',
            'currency_code' => 'alpha | size:3 | in:DZD,USD,EUR'
        ];

        $data = $request->json()->all();

        if (!$this->validateData($data,$rules)) {
            return response()->json(['message' => 'invalid input data'], 400);
        }
        $user = $request->user();

         // Get all accounts of the user
        $accountList = $this->accountsService->findAccountsByUserId($user->id);

        // Add Tharwa Account
        try {
            $action = $this->accountsService->addAccount($accountList, $data['account_type'], $data['currency_code'], $user->id);
            if (! $action){
                return response(json_encode(['message' => 'data not allowed']),403);
            }
        }catch (\Exception $e){
            dispatch(new LogJob($user->email, null , 'account not created', 11,LogJob::FAILED_STATUS));
            return response(json_encode(['message' => $e->getMessage()]),500);
        }
         dispatch(new LogJob($user->email, null, 'account created', 11,LogJob::SUCCESS_STATUS));
         return response(json_encode(['message' => 'account created successfully']),201);
    }

    /**
     * Get the username by id account (current account)
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function getUsernameByIdAccount($id){

        $username = $this->accountsService->getUsernameByIdAccount($id);
        if(!$username){
            return response()->json(['message' => "No current account with this id"], 404);
        }
        return response()->json($username, 200);
    }

    /**
     * @param Request $request
     * @param $id_account
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactions(Request $request, $id_account){
        $user = $request->user();
        $accountList = $this->accountsService->findAccountsByUserId($user->id);
        $account = $this->accountsService->findById($id_account);
        if (! in_array($account,$accountList)){
            return response()->json(['message' => 'Not account of the user'], 404);
        }
        $virementInternesService = new VirementInternesServices();
        $virementInternes = $virementInternesService->getValideTransferByAccountId($id_account);

        return response()->json($virementInternes, 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addJustifAccount(Request $request){
        $rules = [
            'account_type' => 'required | integer | between:1,4',
            'justif' => 'required'
        ];

        $data = $request->json()->all();

        if (!$this->validateData($data,$rules)) {
            return response()->json(['message' => 'invalid input data'], 400);
        }
        $user = $request->user();
        $customer = $user->customer();
        $account = $this->accountsService->findAccountTypeByUserId($customer,$data['account_type']);
        // Check if account exists
        if(is_null($account)){
            return response()->json(['message' => 'type compte non trouv??'], 404);
        }
        // Check if the account is is valide
        if($account->status != 3){
            return response()->json(['message' => 'compte n\'est en mode de blockage'], 405);
        }
        // Check if there is a justif non validated yet
        $justifServices = new JustificationServices();
        $lastJustif = $justifServices->getLastJustifByAccountId($account->id);
        if(!is_null($lastJustif) && $lastJustif->status == 0){
            return response()->json(['message' => 'vous avez d??poser un justif'], 405);
        }
        // Inserer le justif
        try{
            $justifServices->createAccountJustif($account->id,$data['justif']);
            return response()->json(['message' => 'Justif ajout?? avec succes'], 201);
        }catch (\Exception $exception){
            return response()->json(['message' => 'erreur serveur'], 500);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBlockedAccountsToUnblock(Request $request){
        $justifServices = new JustificationServices();
        $customerServices = new CustomersServices();
        try {
            $blockedAccounts = array();
            $invalidatedJustifs = $justifServices->getInvalidatedJustif();
            if (!is_null($invalidatedJustifs)){
                foreach ($invalidatedJustifs as $invJustif){
                    $account = $this->accountsService->findById($invJustif->id_account);
                    $customer = $customerServices->findById($account->id_customer);
                    $account->setAttribute('nom',$customer->name);
                    $account->setAttribute('code',$account->getCode());
                    array_push($blockedAccounts,$account);
                }
            }
            return response()->json($blockedAccounts, 200);
        }catch (\Exception $exception){
            return response()->json(['message' => 'erreur serveur'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $account_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnblockDemandByAccountId(Request $request, $account_id){
        $justifServices = new JustificationServices();
        $accountServ = new AccountsServices();
//        try {
            $invalidatedJustif = $justifServices->getInvalidatedJustifByAccountId($account_id);
            // Get blocking info
            if(!is_null($invalidatedJustif)) {
                $motif = $accountServ->getLastBlockingMotif($invalidatedJustif->id_account);
                $invalidatedJustif->setAttribute('motif_object', $motif['object']);
                $invalidatedJustif->setAttribute('motif_justif', $motif['justification']);
            }
            return response()->json($invalidatedJustif, 200);
//        }catch (\Exception $exception){
//            return response()->json(['message' => 'erreur serveur'], 500);
//        }
    }


    /**
     *
     */
    public function getUnBlockedAccountsToblock(){
        try{
            $accounts = $this->accountsService->findAllAccountsToBlock();
            $customerServices = new CustomersServices();
             if (!is_null($accounts)){
                 foreach ($accounts as $account){
                     $customer = $customerServices->findById($account->id_customer);
                     $account->setAttribute('nom',$customer->name);
                     $account->setAttribute('code',$account->getCode());
                 }
             }
             return response()->json($accounts, 200);
        }catch (\Exception $exception){
            return response()->json(['message' => 'erreur serveur'], 500);
        }
    }

    /**
     * @param Request $request
     * @param id_justif_account
     * @return \Illuminate\Http\JsonResponse
     */
    public function refuseAccountJustif(Request $request, $id_justif_account){
        $justifServices = new JustificationServices();
        try{
            $justif = $justifServices->findById($id_justif_account);
            if (is_null($justif)){
                return response()->json(['message' => 'non justif trouv??'], 404);
            }
            // check if account needs to be refused
            if ($justif->status != 0){
                return response()->json(['message' => 'justif est d??ja accept?? ou r??fus??'], 405);
            }
            $justifServices->disactiverJustif(2,$justif->id_account);
            return response()->json(['message' => 'justif r??fus??'], 200);
        }catch (\Exception $exception){
            return response()->json(['message' => $exception->getMessage()], 500);
        }


    }

}