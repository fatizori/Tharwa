<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\VirementInterne;
use App\Services\AccountsServices;
use  App\Jobs\LogJob;
use App\Services\JustificationServices;
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
        $account = $this->accountsService->findAccountByType($type, $user_id);
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

    public function  validateAccount(Request $request,$id_account){
        //Validation of data
        $rules = [
            'type' => 'numeric',
            'justif_object' => 'max:190',
            'justif' => 'max:255'
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            dispatch(new LogJob('','',"Input validation error",11,LogJob::FAILED_STATUS));
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
                }break;
                case 2:{
                    $message = 'account was unblocked';
                    // unblock an account
                    $action = $this->accountsService->unblockAccount($account);
                }break;
                case 3:
                    {
                        $message = 'account was blocked';
                        // block an account
                        $justif = $data['justif'];
                        $justif_object = $data['justif_object'];
                        if (is_null($justif) || !isset($justif)) {
                            dispatch(new LogJob('', '', "Justif manque", 11, LogJob::FAILED_STATUS));
                            return response()->json(['message' => $validator->errors()->all()], 400);
                        }
                        $action = $this->accountsService->blockAccount($account);
                        if ($action){
                            //Save Banker Operation
                            $this->accountsService->createBankerAction($account, $banker->id, 3,$justif_object,$justif);
                        }
                    }break;
                case 4:{
                    $message = 'new account was refused';
                    // delete new account
                    $action = $this->accountsService->refuseNewAccount($account);
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

}