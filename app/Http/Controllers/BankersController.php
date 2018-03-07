<?php
namespace App\Http\Controllers;


use App\Models\Banker;


class BankersController extends Controller
{
    public function __construct()
    {     
    }

    public function index(){

        //get the list of bankers
        $bankers  = Banker::join('users', 'users.id', '=', 'bankers.id')
           ->select('users.id', 'name','firstname','address','email','phone_number','photo')
            ->get();

        //if no banker exists in the database
        if(!$bankers){
            return response()->json(['message' => 'No banker was found'], 404);
        }
        return response()->json($bankers, 200);
    }


    

    public function show($id){
        $banquier = Banker::find($id);
        if(!$banquier){
            return response()->json(['message' => "The banker with {$id} doesn't exist"], 404);
        }
        return response()->json($banquier, 200);
    }


    

    public function destroy($id){
        $banquier = Bnaquier::find($id);
        if(!$banquier){
            return response()->json(['message' => "The banker with {$id} doesn't exist"], 404);
        }
        $banquier->delete();
        return response()->json(['message' =>"The banker with  id {$id} has been deleted"], 200);
    }


    
}