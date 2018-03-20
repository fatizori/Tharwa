<?php
namespace App\Http\Controllers;

use App\Models\Account;
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
        //get the list of accounts
        $accounts  = Account::get()->all();
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
    public function indexNonValid(){
        //get the list of accounts non valide
        $accounts  = Account::join('customers', 'customers.id', '=', 'accounts.id_customer')
            ->join('users','users.id','=','accounts.id_customer')
            ->where('accounts.status','=',0)
            ->select('accounts.id','name','function','address','phone_number','email','accounts.type')
            ->get();

        //if no account exists in the database
        if(!$accounts){
            return response()->json(['message' => 'No account was found'], 404);
        }
        return response()->json($accounts, 200);
    }




    public function show($id){
        $accounts = Account::find($id);
        if(!$accounts){
            return response()->json(['message' => "The account with {$id} doesn't exist"], 404);
        }
        return response()->json($accounts, 200);
    }



    public function destroy($id){
        $account = Account::find($id);
        if(!$account){
            return response()->json(['message' => "The account with {$id} doesn't exist"], 404);
        }
        try {
            $account->delete();
        } catch (\Exception $e) {
        }
        return response()->json(['message' =>"The acount with  id {$id} has been deleted"], 200);
    }

  /*
   *  Validate the customer account
   */

    public function  validateAccount(Request $request,$id_account){
        $data = $request->json()->all();
        $type = $data['type'];
        // find the account by id
        $account = Account::find($id_account);
        if(!is_null($account)){
            // update the account status
            $account->update(['status'=> $type]);

            return  response()->json(['message' => 'status account has been updated successfully '], 200);
        }else{
            return response()->json(['message' => 'account not found'], 404);
        }

    }
}