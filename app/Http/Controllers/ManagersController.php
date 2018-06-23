<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 05-03-2018
 * Time: 14:03
 */

namespace App\Http\Controllers;
use App\Models\Manager;
use App\Services\ManagersServices;
use Illuminate\Http\Request;


class ManagersController extends Controller
{
    private $managerServices;

    public function __construct()
    {
        $this->managerServices = new ManagersServices();
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
        $keys = ['id', 'name','firstname','photo', 'address'];
        $needed_manager = array();
        foreach ($keys as $key) {
            $needed_manager[$key] = $manager[$key];
        }
        $user = $manager->user()->getAttributes();
        $keys = ['email','phone_number'];
        foreach ($keys as $key) {
            $needed_manager[$key] = $user[$key];
        }
        $needed_manager['photo'] = FilesController::generateNameImageMinUser($manager['id'],$manager['photo']);
        return response()->json($needed_manager, 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeInfo(Request $request){
        //Validate input
        $rules = [
            'name' => 'alpha',
            'firstname'=> 'alpha',
            'address' => '',
            'email' => 'email | filled',
            'phone_number' => 'numeric | filled | min:12',
        ];
        $data = $request->json()->all();

        if(! $this->validateData($data,$rules)){
            return response()->json(['message' => 'invalid input data'], 400);
        }

        //call for update infos
        return $this->managerServices->updateInfo($request);
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