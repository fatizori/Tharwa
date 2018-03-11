<?php namespace App\Http\Controllers;

use  App\Http\Controllers\FilesController;
use  App\Http\Controllers\UsersController;
use App\Models\User;
use App\Models\Account;
use App\Models\Banker;
use App\Models\Customer;
use \Illuminate\Http\Request;
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


    try{

        DB::beginTransaction();

          $user = new UsersController;
          $file = new FilesController;
    
        //Create a new user
       $user_id = $user->store($request,0);

         //Validation of data for Customer
       $rulesCustomer = [
            'name' => 'required',
            'address' => 'required',
            'function'=>'required',
            'wilaya'=>'required',
            'commune'=>'required',
            'type'=>'required',

        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rulesCustomer);
        if (!$validator->passes()) {
             return   response()->json(['message' => $validator->errors()->all()], 400);

        }

        //picture validation
       $photoRule =[
           'photo' => 'required|image|mimes:jpeg,png,jpg,bmp|max:2048',
       ];
        $this->validate($request,$photoRule);
        //Client Traitement
        $customer  = new Customer();
        $customer->name = strip_tags($data['name']);
        $customer->address = strip_tags($data['address']);
        $customer->function = strip_tags($data['function']);
        $customer->wilaya = strip_tags($data['wilaya']);
        $customer->commune = strip_tags($data['commune']);
        $customer->type = $data['type'];
        $customer->id = $user_id;

        //customer avatar
        /*$path = base_path('public/test/test.png');
        $photo = new UploadedFile($path, 'test.png', 'image/png', null, UPLOAD_ERR_OK, true);*/
        $picture_url = $file->uploadImage($request->file('photo'),self::IMAGE_USER,self::IMAGE_MIN,$user_id);
        $customer->photo= $picture_url ;
        $customer->save();
    
        
        //Account Traitement
         $this->createAccount($user_id,0); //the default account is the current account

        DB::commit();
        return response(json_encode(['message' => 'new user  has been registered']),201);
  
    } catch(\Exception $e){
        DB::rollback();

        echo $e->getMessage();
    }
    
}
//****************************    Create the banker account   ******************************//

    /**
     * Create the banker account
     * @param Request $request , $id_manager
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */

     public function registerBanker(Request $request){
         //Valdiation Of data Banker
         $rulesBanker = [
             'name' => 'required',
             'firstname' => 'required',
             'address'=>'required'
         ];

         $data=$request->json()->all();
         $validator = Validator::make($data, $rulesBanker);
         if (!$validator->passes()) {
             return response()->json(['message' => $validator->errors()->all()], 400);
         }

       try{
        DB::beginTransaction();
        //Get the id of the creator (manager)
        $id_manager = $request->user()->id;

        $user = new UsersController;
        //Create a new user
        $user_id = $user->store($request,1);

        //Banker Traitement 
        $banker=new Banker();
        $banker->name=strip_tags($data['name']);
        $banker->firstname=strip_tags($data['firstname']);
        $banker->address=strip_tags($data['address']);
        $banker->id=$user_id;
        $banker->photo=self::DEFAULT_USER_IMG;
        $banker->id_creator = $id_manager;  // the id of the manager who create the banker account
        $banker->save();

         DB::commit();
         return response(json_encode(['message' => 'new user  has been registered']),201);

      } catch(\Exception $e){
        DB::rollback();

        echo $e->getMessage();
      }     
}

}
