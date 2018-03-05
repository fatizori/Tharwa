<?php
namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;


class AccountsController extends Controller
{
    public function __construct()
    {      
    }

    public function index(){
        $accounts = Account::all();
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


    

}