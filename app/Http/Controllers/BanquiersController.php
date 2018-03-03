<?php
namespace App\Http\Controllers;

use App\Models\Banquier;
use Illuminate\Http\Request;


class BanquiersController extends Controller
{
    public function __construct()
    {     
    }

    public function index(){
        $banquiers = Banquier::all();
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