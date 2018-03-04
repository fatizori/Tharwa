<?php namespace App\Http\Controllers;

use  App\Http\Controllers\FilesController;
use  App\Http\Controllers\UsersController;
use App\Models\User;
use App\Models\Compte;
use App\Models\Banquier;
use App\Models\Customer;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;


class RegistersController extends Controller {
    
    public function __construct()
    {

     
    }

    //****************************    Creat a customer account    ******************************//
    /**
     * Create bank account for a customer
     * @param user_id , accoutn type
     */
    public function createAccount($id,$type){
        $account=new Compte();
        $account->id_client=$id;
        $account->type = $type;
        $account->save();
    }

    /**
     * Create the customer account
     * @param Request $request
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
            'nom' => 'required',
            'adresse' => 'required',
            'telephone'=>'required',
            'fonction'=>'required',
            'wilaya'=>'required',
            'commune'=>'required',
            'type'=>'required',

        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rulesCustomer);
        if (!$validator->passes()) {
            return   response()->json(['message' => $validator->errors()->all()], 400);

        }

       $photoRule =[
           'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
       ];
        $this->validate($request,$photoRule);
        //Client Traitement
        $customer  = new Customer();
        $customer->nom = strip_tags($data['nom']);
        $customer->adresse = strip_tags($data['adresse']);
        $customer->telephone = strip_tags($data['telephone']);
        $customer->fonction = strip_tags($data['fonction']);
        $customer->wilaya = strip_tags($data['wilaya']);
        $customer->commune = strip_tags($data['commune']);
        $customer->type = $data['type'];
        $customer->id = $user_id;

        //customer avatar
        /*$path = base_path('public/test/test.png');
        $photo = new UploadedFile($path, 'test.png', 'image/png', null, UPLOAD_ERR_OK, true);*/
        $picture_url = $file->uploadFile($request->file('photo'),'images/customer/','images/customer_min/');
        $customer->photo= $picture_url ;
        $customer->save();
    
        
        //Account Traitement
        $this->createAccount($user_id,0); //the default account is the current account

        DB::commit();
        return response(json_encode(['message' =>"new user  has been registered"]),201);
  
    } catch(\Exception $e){
        DB::rollback();

        echo $e->getMessage();
    }
    
}
//****************************    Create the banker account   ******************************//
    /**
     * Create the banker account
     * @param Request $request , $id_manager
     */

     public function registerBanker(Request $request,$id_manager){

       try{

        DB::beginTransaction();

        $user = new UsersController;
        //Create a new user
        $user_id = $user->store($request,0);

         //Valdiation Of data Banker
         $rulesBanker = [
            'nom' => 'required',   
            'prenom' => 'required',
            
        ];

        $data=$request->json()->all();
        $validator = Validator::make($data, $rulesBanker);
        if (!$validator->passes()) {
            return response()->json(['message' => $validator->errors()->all()], 400);
        }
        //Banker Traitement 
        $banker=new Banquier();
        $banker->nom=strip_tags($data['nom']);
        $banker->prenom=strip_tags($data['prenom']);
        $banker->id=$user_id;
        $banker->id_createur = $id_manager;  // the id of the manager who create the banker account
        $banker->save();

         DB::commit();
         return response(json_encode(['message' =>"new user  has been registered"]),201);

      } catch(\Exception $e){
        DB::rollback();

        echo $e->getMessage();
      }     
}

}
