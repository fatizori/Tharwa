<?php
namespace App\Http\Controllers;
use App\Services\UsersServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function __construct()
    {       //TODO the middleware is applied before in the route but we can do this
           // $this->middleware('auth', ['except' => ['index' , 'show']]);
    }

    /**
     * Find all users
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $userService = new UsersServices();
        $users = $userService->findAll();
        return response()->json($users, 200);
    }

    /**
     * Find a user by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){
        $userService = new UsersServices();
        $user = $userService->findById($id);
        if(!$user){
            return response()->json(['message' => "The user with {$id} doesn't exist"], 404);
        }
        return response()->json($user, 200);
    }

    /**
     * Create a new User
     * @param Request $request, user role
     * @return integer id
     */
    public function store(Request $request,$role){
        $this->validateRequest($request);
        $data =  $request->json()->all();
        $userService = new UsersServices();
        $user = $userService->create($data,$role);
        return $user->id;
    }


    /**
     * Update the user data
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        $userService = new UsersServices();
        //find the user by id
        $user = $userService->findById($id);
        //if the user not exist
        if(!$user){
            return response()->json(['message' => "The user with {$id} doesn't exist"], 404);
        }
        //if the user exist validate the data
        $this->validateRequest($request);
        $data = $request->json()->all();
        //update the user data
         $userService->update($user,$data);

        return response()->json(['message' => "The user with  id {$user->id} has been updated"], 200);
    }

    /**
     * Delete a user
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id){
        $userService = new UsersServices();
        //find the user by id
        $user = $userService->findById($id);
        if(!$user){
            return response()->json(['message' => "The user with {$id} doesn't exist"], 404);
        }
        //delete the user
       $userService->delete($user,$id);
        return response()->json(['message' =>"The user with  id {$id} has been deleted"], 200);
    }

    /**
     * Input validation for user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateRequest(Request $request){
        $rules = [
            'email' => 'required| email',
            'password' => 'required',
            'phone_number'=> 'required'
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            return  response()->json(['message' => $validator->errors()->all()], 400);

        }
    }

}