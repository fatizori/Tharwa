<?php
namespace App\Http\Controllers;

use App\Models\Compte;
use Illuminate\Http\Request;


class ComptesController extends Controller
{
    public function __construct()
    {      
    }

    public function index(){
        $comptes = Compte::all();
        return response()->json($comptes, 200);
    }


   

    public function show($id){
        $compte = Compte::find($id);
        if(!$compte){
            return response()->json(['message' => "The account with {$id} doesn't exist"], 404);
        }
        return response()->json($compte, 200);
    }


    

    public function destroy($id){
        $compte = Compte::find($id);
        if(!$compte){
            return response()->json(['message' => "The account with {$id} doesn't exist"], 404);
        }
        $compte->delete();
        return response()->json(['message' =>"The acount with  id {$id} has been deleted"], 200);
    }


    

}