<?php namespace App\Http\Controllers;

use  App\Http\Controllers\FilesController;
use  App\Http\Controllers\UsersController;
use  App\Jobs\LogJob;
use App\Models\User;
use App\Models\Account;
use App\Models\Banker;
use App\Models\Customer;
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
        $account=new Account();
        $account->id_customer=$id;
        $account->type = $type;
        $account->save();
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

            return   response()->json(['message' => $validator->errors()->all()], 400);
        }


     try{
        DB::beginTransaction();

        //Create a new user
       $user = new UsersController;
       $user_id = $user->store($request,0);
        //Client Traitement
        $customer  = new Customer();
        $customer->name = strip_tags($data['name']);
        $customer->address = strip_tags($data['address']);
        $customer->function = strip_tags($data['function']);
        $customer->wilaya = strip_tags($data['wilaya']);
        $customer->commune = strip_tags($data['commune']);
        $customer->type = $data['type'];
        $customer->photo=self::DEFAULT_USER_IMG;
        $customer->id = $user_id;
        $customer->save();
        //Account Traitement
         $this->createAccount($user_id,0); //the default account is the current account

        DB::commit();

         //log information
         dispatch(new LogJob($data['email'],'',"a new customer was created",1,"success"));

        return response(json_encode(['message' => 'new user  has been registered',
                                     'user_id' => $user_id]),201);

  
    } catch(\Exception $e){

        DB::rollback();
         // log information
         dispatch(new LogJob($data['email'],'',$e->getMessage(),1,"failed"));
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
             return response()->json(['message' => $validator->errors()->all()], 400);
         }

         try {
             DB::beginTransaction();
             //Get the id of the creator (manager)
             $id_manager = $request->user()->id;

             $user = new UsersController;
             //Create a new user
             $user_id = $user->store($request, 1);

             //Banker Traitement
             $banker = new Banker();
             $banker->name = strip_tags($data['name']);
             $banker->firstname = strip_tags($data['firstname']);
             $banker->address = strip_tags($data['address']);
             $banker->id = $user_id;
             $banker->photo = self::DEFAULT_USER_IMG;
             $banker->id_creator = $id_manager;  // the id of the manager who create the banker account
             $banker->save();

             DB::commit();
             return response(json_encode(['message' => 'new user  has been registered',
                                            'user_id' => $user_id]), 201);

         } catch (\Exception $e) {
             DB::rollback();

             return response()->json(['message' => $e->getMessage()], 500);
         }
     }


    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
     public function update_avatar(Request $request){
         // Handle the user upload of avatar
         $data['id_user'] = $request->input('id_user');
         $data['photo'] = $request->file('photo');
         $rules =[
                 'id_user' => 'required',
                 'photo' => 'required|image|mimes:jpeg,png,jpg,bmp|max:2048'
         ];

         $validator = Validator::make($data, $rules);

         if (!$validator->passes()) {
             return response()->json(['message' => $validator->errors()->all()], 400);
         }

         $file = new FilesController;

         //customer avatar
         $id_user = $data['id_user'];
         $picture_url = $file->uploadImage($data['photo'],self::IMAGE_USER,self::IMAGE_MIN,$id_user);
         $user = User::find($id_user);
         if (!is_null($user)){
             switch ($user->getRole()){
                 case  'customer': {
                    Customer::find($id_user)->update(['photo' => $picture_url]);
                 }break;
                 case  'banker': {
                    Banker::find($id_user)->update(['photo' => $picture_url]);
                 }break;
                 default: return  response()->json(['message' => 'invalid user'], 400);
             }
             return  response()->json(['message' => 'photo has been updated successfully'], 200);
         }else{
             return response()->json(['message' => 'user not found'], 404);
         }
     }


}
