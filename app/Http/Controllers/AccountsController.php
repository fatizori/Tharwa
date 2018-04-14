<?php
namespace App\Http\Controllers;

use App\Services\AccountsServices;
use  App\Jobs\LogJob;
use Illuminate\Http\Request;


class AccountsController extends Controller
{
    public function __construct()
    {      
    }

    /**
     * Get all accounts
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $accountService = new AccountsServices();
        //get the list of accounts
        $accounts = $accountService->findAll();
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
        $accountService = new AccountsServices();
        //get the list of accounts non valide
        $accounts = $accountService->getInvalidAccounts();

        //if no account exists in the database
        if(!$accounts){

            return response()->json(['message' => 'No account was found'], 404);
        }

        return response()->json($accounts, 200);
    }


    /**
     * get an account by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){

        $accountService = new AccountsServices();
        $account = $accountService->findById($id);
        if(!$account){
            return response()->json(['message' => "The account with {$id} doesn't exist"], 404);
        }
        return response()->json($account, 200);
    }


    /**
     * Delete an account
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        $accountService = new AccountsServices();
        //find the account
        $account = $accountService->findById($id);
        if(!$account){
            return response()->json(['message' => "The account with {$id} doesn't exist"], 404);
        }
        // delete the account
        $accountService->delete($account);

        return response()->json(['message' =>"The acount with  id {$id} has been deleted"], 200);
    }

    /**
     * Validate the customer account
     * @param Request $request
     * @param $id_account
     * @return \Illuminate\Http\JsonResponse
     */

    public function  validateAccount(Request $request,$id_account){
        $data = $request->json()->all();

        //Get the email of the banker
        $email_banker = $request->user()->email;
        $type = $data['type'];
        $accountService = new AccountsServices();
        //find the account
        $account = $accountService->findById($id_account);

        if(!is_null($account)){
            // update the account status
            $accountService->updateAccountStatus($account,$type);
            // log information
            dispatch(new LogJob($id_account,$email_banker,'an account was updated',4,LogJob::SUCCESS_STATUS));
            return  response()->json(['message' => 'status account has been updated successfully '], 200);
        }else{
            dispatch(new LogJob($id_account,$email_banker,'account not found',4,LogJob::FAILED_STATUS));
            return response()->json(['message' => 'account not found'], 404);
        }

    }
}