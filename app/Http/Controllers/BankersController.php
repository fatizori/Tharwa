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
        $banker = Banker::find($id);
        if(!$banker){
            return response()->json(['message' => "The banker with {$id} doesn't exist"], 404);
        }
        $keys = ['id', 'name','firstname','photo'];
        $needed_banker = array();
        foreach ($keys as $key) {
            $needed_banker[$key] = $banker[$key];
        }
        $needed_banker['photo'] = FilesController::generateNameImageMinUser($banker['id'],$banker['photo']);
        //$needed_banker['photo'] =  'min_avatar_c6bb6cae57f0a474d1f622f385546cf7cfd26c84_ba33a1812f10c5a2b9fa53f59e4c89bb47006076a7f7f94c3fd670e723e55c69b3badb02.jpeg';

        return response()->json($needed_banker, 200);
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