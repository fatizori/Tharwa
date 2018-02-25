<?php namespace App\Http\Controllers;

use App\Models\User;
use \Illuminate\Http\Request;
use \Laravel\Passport\Client;

class RegistersController extends Controller {
    private $client;
    public function __construct()
    {
        $this->client=Client::find(2);
    }


    public function register(Request $request){

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required| email ',
            'password' => 'required'
        ]);
        $user=new User();
        $user->name=$request->input('name');
        $user->email=$request->input('email');
        $user->password=app('hash')->make($request->input('password'));
        $user->save();
        //return response()->json($user,200);

        $params=[
            'grant-type' => 'password',
            'client-id' => $this->client->id,
            'secret' => $this->client->secret,
            'user-name' => $user['email'],
            'password' => $request['password'],
            'scope' => '*'
         ];
        $request->request->add($params);
       // $proxy = Request::create('oauth/accesToken','POST');

        return redirect()->to('oauth/accesToken');
        return dispatch($proxy);

    }
}
