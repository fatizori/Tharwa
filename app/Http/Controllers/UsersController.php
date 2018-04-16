<?php
namespace App\Http\Controllers;
use App\Services\UsersServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    private $userService;
    public function __construct()
    {       //TODO the middleware is applied before in the route but we can do this
           // $this->middleware('auth', ['except' => ['index' , 'show']]);
        $this->userService = new UsersServices();
    }

    /**
     * Find all users
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $users = $this->userService->findAll();
        return response()->json($users, 200);
    }

    /**
     * Find a user by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){
        $user = $this->userService->findById($id);
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
        $user = $this->userService->create($data,$role);
        return $user->id;
    }


    /**
     * Update the user data
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        //find the user by id
        $user = $this->userService->findById($id);
        //if the user not exist
        if(!$user){
            return response()->json(['message' => "The user with {$id} doesn't exist"], 404);
        }
        //if the user exist validate the data
        $this->validateRequest($request);
        $data = $request->json()->all();
        //update the user data
         $this->userService->update($user,$data);

        return response()->json(['message' => "The user with  id {$user->id} has been updated"], 200);
    }

    /**
     * Delete a user
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id){
        //find the user by id
        $user = $this->userService->findById($id);
        if(!$user){
            return response()->json(['message' => "The user with {$id} doesn't exist"], 404);
        }
        //delete the user
       $this->userService->delete($user,$id);
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

    /**
     * Update the user password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request){
        //Validate input
        $rules = [
            'old_password' => 'required',
            'new_password'=> 'required'
        ];
        $data = $request->json()->all();

        // validate  data
        if(! $this->validateData($data,$rules)){
            return response()->json(['message' => 'invalid input data'], 400);
        }

        //update the user password
        if (! $this->userService->updatePassword($request)){
            return response()->json(['message' => 'Old password incorrect'], 400);
        }

        return response()->json(['message' => 'Password changed!'], 200);
    }

}