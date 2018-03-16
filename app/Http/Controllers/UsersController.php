<?php
namespace App\Http\Controllers;

use App\Business\LogManager;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function __construct()
    {       //TODO the middleware is applied before in the route but we can do this
           // $this->middleware('auth', ['except' => ['index' , 'show']]);
    }

    public function index(){
        $users = User::all();
        return response()->json($users, 200);
    }


    /**
     * Create a new User
     * @param Request $request, user role
     * @return integer id
     */
    public function store(Request $request,$role){
        $this->validateRequest($request);
        $data =  $request->json()->all();
        $user = User::create([
            'email' => $data['email'],
            'password'=> app('hash')->make($data['password']),
            'phone_number'=>$data['phone_number'],
            'role'=>$role,
            'nonce_auth' => sprintf('%04u', random_int(0,9999)),
            'expire_date_nonce' => Carbon::now()->addHours(1)->toDateTimeString()
        ]);

        
        return $user->id;
    }

    public function show($id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => "The user with {$id} doesn't exist"], 404);
        }
        return response()->json($user, 200);
    }


    public function update(Request $request, $id){

        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => "The user with {$id} doesn't exist"], 404);
        }
        $this->validateRequest($request);
        $user->email = $request->get('email');
        $user->password = app('hash')->make($request->get('password'));
        $user->save();
        return response()->json(['message' => "The user with  id {$user->id} has been updated"], 200);
    }

    public function destroy($id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['message' => "The user with {$id} doesn't exist"], 404);
        }
        $user->delete();
        return response()->json(['message' =>"The user with  id {$id} has been deleted"], 200);
    }

    /**
     * Ipnut validation for user
     */
    public function validateRequest(Request $request){
        $rules = [
            'email' => 'required| email',
            'password' => 'required',
           
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            return  response()->json(['message' => $validator->errors()->all()], 400);

        }
    }

}