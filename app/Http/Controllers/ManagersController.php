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
            ->select('users.id', 'name','firstname','address','email','phone_number','photo')
            ->get();

        //if no manager exists in the database
        if(!$managers){
            return response()->json(['message' => 'No manager was found'], 404);
        }
        return response()->json($managers, 200);
    }




    public function show($id){
        $manager = Manager::find($id);

        if(!$manager){
            return response()->json(['message' => "The manager with {$id} doesn't exist"], 404);
        }
        $keys = ['id', 'name','firstname','photo'];
        $needed_manager = array();
        foreach ($keys as $key) {
            $needed_manager[$key] = $manager[$key];
        }
       // $needed_manager['photo'] = FilesController::generateNameImageMinUser($manager['id'],$manager['photo']);
        $needed_manager['photo'] =  'mini_tom.jpg';
        return response()->json($needed_manager, 200);
    }




    public function destroy($id){
        $manager = Manager::find($id);
        if(!$manager){
            return response()->json(['message' => "The manager with {$id} doesn't exist"], 404);
        }
        $manager->delete();
        return response()->json(['message' =>"The manager with  id {$id} has been deleted"], 200);
    }



}