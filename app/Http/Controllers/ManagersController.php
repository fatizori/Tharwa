<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 05-03-2018
 * Time: 14:03
 */

namespace App\Http\Controllers;
use App\Models\Manager;


class ManagersController extends Controller
{
    public function __construct()
    {
    }

    public function index(){

        //get the list of managers
        $managers  = Manager::join('users', 'users.id', '=', 'managers.id')
            ->select('users.id', 'nom','prenom','adresse','email','phone_number','photo')
            ->get();

        //if no manager exists in the database
        if(!$managers){
            return response()->json(['message' => "No manager was found"], 404);
        }
        return response()->json($managers, 200);
    }




    public function show($id){
        $manager = Manager::find($id)
                   ->select('id', 'nom','prenom','photo')
                   ->get();
        if(!$manager){
            return response()->json(['message' => "The manager with {$id} doesn't exist"], 404);
        }
        return response()->json($manager, 200);
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