<?php namespace App\Http\Controllers;

use App\Models\User;
use \Illuminate\Http\Request;

class RegistersController extends Controller {

    public function __construct()
    {
    }

    public function register(Request $request){

        //Validation of data
        $rules = [
            'email' => 'required| email',
            'password' => 'required',
            'role' => 'required| regex:([(customer)(banker)]+)',
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            return response()->json(['message' => $validator->errors()->all()], 400);
        }

        //Traitement
        $user=new User();
        $user->email=$request->input('email');
        $user->password=app('hash')->make($request->input('password'));
        $user->role=$request->input('role');
        $user->save();

        $params=[
            'username' => $user['email'],
            'password' => $user['password'],
            'scope' => $user['role']
         ];
        //TODO Login after successful registration, login directly?

        return response(json_encode(['message' =>"new user with id={$user->id} has been registered"]),201);
    }
}
