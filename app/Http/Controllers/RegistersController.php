<?php namespace App\Http\Controllers;

use App\Services\AccountsServices;
use App\Services\CustomersServices;
use App\Services\BankersServices;
use App\Services\UsersServices;
use  App\Http\Controllers\FilesController;
use  App\Http\Controllers\UsersController;
use  App\Jobs\LogJob;
use \Illuminate\Http\Request;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;


class RegistersController extends Controller {

    const IMAGE_USER = 'images/customer/';
    const IMAGE_MIN = 'images/customer_min/';
    const DEFAULT_USER_IMG = 'default_user.png';

    public function __construct()
    {

    }

    //****************************    Creat a customer account    ******************************//
    /**
     * Create bank account for a customer
     * @param user_id , accoutn type
     */
    public function createAccount($id,$type){
        $accountServices = new AccountsServices();
        $accountServices->create($id,$type);
    }

    /**
     * Create the customer account
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function registerCustomer(Request $request){
        //Validation of data for Customer
        $rulesCustomer = [
            'name' => 'required',
            'address' => 'required',
            'function'=>'required',
            'wilaya'=>'required',
            'commune'=>'required',
            'type'=>'required | integer | between:0,1',
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rulesCustomer);
        if (!$validator->passes()) {
            // log information
            dispatch(new LogJob($data['email'],'',"Input validation error",1,LogJob::FAILED_STATUS));
            return   response()->json(['message' => $validator->errors()->all()], 400);
        }


     try{
        DB::beginTransaction();

        //Create a new user
       $user = new UsersController;
       $user_id = $user->store($request,0);
        //Client Traitement
        $customerService = new CustomersServices();
        $customerService->create($data,$user_id,self::DEFAULT_USER_IMG);
        //Account Traitement
         $this->createAccount($user_id,1); //the default account is the current account

        DB::commit();

         //log information
         dispatch(new LogJob($data['email'],'',"a new customer was created",1,LogJob::SUCCESS_STATUS));

        return response(json_encode(['message' => 'new user  has been registered',
                                     'user_id' => $user_id]),201);

  
    } catch(\Exception $e){

        DB::rollback();
         // log information
         dispatch(new LogJob($data['email'],'',$e->getMessage(),1,LogJob::FAILED_STATUS));
         // show the exception message
         return response()->json(['message' => $e->getMessage()], 500);
    }
    
}
//****************************    Create the banker account   ******************************//

    /**
     * Create the banker account
     * @param Request $request , $id_manager
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */

     public function registerBanker(Request $request)
     {
         //Valdiation Of data Banker
         $rulesBanker = [
             'name' => 'required',
             'firstname' => 'required',
             'address' => 'required'
         ];

         $data = $request->json()->all();
         $validator = Validator::make($data, $rulesBanker);
         if (!$validator->passes()) {
             // log information
             dispatch(new LogJob($data['email'],'',"Input validation error",1,LogJob::FAILED_STATUS));
             return response()->json(['message' => $validator->errors()->all()], 400);
         }

         try {
             DB::beginTransaction();
             //Get the id of the creator (manager)
             $id_manager = $request->user()->id;
             //Get the email of the creator
             $email_manager = $request->user()->email;

             $user = new UsersController;
             //Create a new user
             $user_id = $user->store($request, 1);

             //Banker Traitement
             $bankerService = new BankersServices();
             $bankerService->create($data,$user_id,$id_manager,self::DEFAULT_USER_IMG);

             DB::commit();

             //log information
             dispatch(new LogJob($data['email'],$email_manager,"a new banker was created",1,LogJob::SUCCESS_STATUS));
             return response(json_encode(['message' => 'new user  has been registered',
                                            'user_id' => $user_id]), 201);

         } catch (\Exception $e) {
             DB::rollback();
             //log information
             dispatch(new LogJob($data['email'],$email_manager,$e->getMessage(),1,LogJob::FAILED_STATUS));
             return response()->json(['message' => $e->getMessage()], 500);         }
     }


    /**
     * Update the user avatar
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
     public function update_avatar(Request $request){

         $customerService = new CustomersServices();
         $bankerService = new BankersServices();
         $userService = new UsersServices();

         // Handle the user upload of avatar
        $data['id_user'] = $request->input('id_user');
         $data['photo'] = $request->file('photo');
         $rules =[
                 'id_user' => 'required',
                 'photo' => 'required|image|mimes:jpeg,png,jpg,bmp|max:2048'
         ];

         $validator = Validator::make($data, $rules);

         if (!$validator->passes()) {
             // log information
             dispatch(new LogJob('','',"Input validation error",3,LogJob::FAILED_STATUS));
             return response()->json(['message' => $validator->errors()->all()], 400);
         }

         $file = new FilesController;

         //customer avatar
         $id_user = $data['id_user'];
         $picture_url = $file->uploadImage($data['photo'],self::IMAGE_USER,self::IMAGE_MIN,$id_user);

         //find the user by id
         $user = $userService->findById($id_user);
         if (!is_null($user)){
             switch ($user->getRole()){
                 case  'customer': {
                    $customerService->updatePhoto( $id_user,$picture_url);
                     break;
                 }
                 case  'banker': {
                    $bankerService->updatePhoto($id_user,$picture_url);
                     break;
                 }
                 default:
                     {
                         // log information
                         dispatch(new LogJob($user->email,'',"invalid user",3,LogJob::FAILED_STATUS));
                         return response()->json(['message' => 'invalid user'], 400);
                     }
             }
             //log information
             dispatch(new LogJob($user->email,'',"a user avatar has been updated",3,LogJob::SUCCESS_STATUS));
             return  response()->json(['message' => 'photo has been updated successfully'], 200);
         }else{
             // log information
             dispatch(new LogJob('','',"user not found",3,LogJob::FAILED_STATUS));
             return response()->json(['message' => 'user not found'], 404);
         }
     }


}
