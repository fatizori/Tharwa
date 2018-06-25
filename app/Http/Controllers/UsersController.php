<?php
namespace App\Http\Controllers;
use App\Jobs\LogJob;
use App\Models\User;
use App\Services\BankersServices;
use App\Services\CustomersServices;
use App\Services\ManagersServices;
use App\Services\PushNotificationService;
use App\Services\UsersServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    const IMAGE_USER = 'images/customer/';
    const IMAGE_MIN = 'images/customer_min/';
    const DEFAULT_USER_IMG = 'default_user.png';

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
     * @param Request $request , user role
     * @param $role
     * @return integer id
     */
    public function store(Request $request,$role){
        $this->validateRequest($request);
        $data =  $request->json()->all();
        $user = User::where('email',$data['email'])->get()->first();
        if ($user){
            return false;
        }
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
     * Update the user data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhoto(Request $request)
    {
        //find the user by id
        $user = $request->user();
        $data['photo'] = $request->file('photo');
        $rules = [
            'photo' => 'required|image|mimes:jpeg,png,jpg,bmp|max:2048'
        ];
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            // log information
            return response()->json(['message' => $validator->errors()->all()], 400);
        }
        $file = new FilesController;
        $picture_url = $file->uploadImage($data['photo'], self::IMAGE_USER, self::IMAGE_MIN, $user->id);

        switch ($user->getRole()) {
            case  'customer':
                {
                    $customerService = new CustomersServices();
                    $customerService->updatePhoto($user->id, $picture_url);
                    break;
                }
            case  'banker':
                {
                    $bankerService = new BankersServices();
                    $bankerService->updatePhoto($user->id, $picture_url);
                    break;
                }
            case 'manager':
                {
                    $managerService = new ManagersServices();
                    $managerService->updatePhoto($user->id, $picture_url);
                    break;
                }
            default:
        }
        //log information
        dispatch(new LogJob($user->email, '', 'a user avatar has been updated', 3, LogJob::SUCCESS_STATUS));
        return response()->json(['message' => 'photo has been updated successfully'], 200);
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerFCMToken(Request $request){
        $user = $request->user();
        $token = $request->input('token');
        $user->fcm_token = $token;
        $user->save();
        $serviceNotif = new PushNotificationService();
        $serviceNotif->sendPushStream($user->id);
        return response()->json(['message' => 'token changed! '.$user->email] , 200);
    }

}