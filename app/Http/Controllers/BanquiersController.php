<?php
namespace App\Http\Controllers;

use  App\Http\Controllers\UsersController;

use App\Models\Banquier;
use Illuminate\Http\Request;


class BanquiersController extends Controller
{
    public function __construct()
    {     
    }

    public function index(){

        //get the list of bankers
        $banquiers  = Banquier::join('users', 'users.id', '=', 'banquiers.id')
           ->select('users.id', 'nom','prenom','adresse','email','phone_number','photo')
            ->get();

        //if no banker exists in the database
        if(!$banquiers){
            return response()->json(['message' => "No banker was found"], 404);
        }
        return response()->json($banquiers, 200);
    }


    

    public function show($id){
        $banquier = Banquier::find($id);
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