<?php namespace App\Http\Controllers;

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

    /**
     * Image Optimization
     * @param $old_image
     * @param $new_image_path   lien image destination
     * @param $new_image_name   nom image destination
     * @param $width             les nouveaux
     * @param $height
     * @return boolean
     */
    public function insertMiniImage($old_image,$new_image_path,$new_image_name,$width,$height){

        $type_old_image = mime_content_type($old_image);

        switch ($type_old_image) {
            case 'image/jpeg' :
                $src_img = imagecreatefromjpeg($old_image);
                break;
            case 'image/png' :
                $src_img = imagecreatefrompng($old_image);
                break;
            case 'image/jpg' :
                $src_img = imagecreatefromjpeg($old_image);

                break;
            default:
                return false;
                break;
        }
        if(!$src_img) {
            return 0;
        }
        list($old_width, $old_height) = getimagesize($old_image);

        $ratio_orig = $old_width/$old_height;

          if ($width/$height > $ratio_orig) {
            $width = $height*$ratio_orig;
        } else {
            $height = $width/$ratio_orig;
        }
        //pour ne pas perdre la qualité
        $thumbnail = imagecreatetruecolor($width,$height);
        if(!$thumbnail) {
            return 0;
        }
        //Resizing
        $result = imagecopyresampled($thumbnail, $src_img, 0, 0, 0, 0, $width, $height, $old_width, $old_height);
        if(!$result) {
            return 0;
        }
        //Sauvgarde d'image
        $result = imagejpeg($thumbnail, $new_image_path.$new_image_name,100);
        if(!$result) {
            return 0;
        }
        //Supprimer l'instance temporaire
        $result = imagedestroy($thumbnail);
        if(!$result) {
            return 0;
        }
        return 1;
    }

    /**
     * Create bank account for a customer
     */
    public function createAccount($id,$type){
        $account=new Compte();

        $account->id_client=$id;
        $account->type = $type;

        $account->save();
    }

    /**
     * Create the customer account
     *
     */
    public function registerCustomer(Request $request){

    try{

        DB::beginTransaction();

        //Validation of data for User
        $rules = [
            'email' => 'required| email',
            'password' => 'required',
           
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            return  response()->json(['message' => $validator->errors()->all()], 400);

        }

        //user Traitement 
        $user=new User();
        $user->email=$request->input('email');
        $user->password=app('hash')->make($request->input('password'));
        $user->role = 0;                              // role = 0 for the customer
        $user->save();

         //Validation of data for Customer
       $rulesCustomer = [
            'nom' => 'required',
            'adresse' => 'required',
            'fonction'=>'required',
            'wilaya'=>'required',
            'commune'=>'required',
            'type'=>'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rulesCustomer);
        if (!$validator->passes()) {
            return   response()->json(['message' => $validator->errors()->all()], 400);

        }

    
        //Client Traitement
        $customer  = new Customer();
        $customer->nom = strip_tags($request->input('nom'));
        $customer->adresse = strip_tags($request->input('adresse'));
        $customer->telephone = strip_tags($request->input('telephone'));
        $customer->fonction = strip_tags($request->input('fonction'));
        $customer->wilaya = strip_tags($request->input('wilaya'));
        $customer->commune = strip_tags($request->input('commune'));
        $customer->type = $request->input('type');

        $customer->id = $user->id;

        //customer avatar
        /*$path = base_path('public/test/test.png');
        $photo = new UploadedFile($path, 'test.png', 'image/png', filesize($path), UPLOAD_ERR_OK, true);*/
        $image = $request->file('photo');
        $imagename = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = 'images/';
        $image->move($destinationPath, $imagename);
        $this->insertMiniImage($destinationPath.$imagename,'uploads/',$imagename,100,100);
        $customer->photo= $destinationPath.$imagename ;
        $customer->save();
    
        
        //Account Traitement
       RegistersController::createAccount($user->id,0); //the default account is the current account

        DB::commit();
        return response(json_encode(['message' =>"new user  has been registered"]),201);
  
    } catch(\Exception $e){
        DB::rollback();

        echo $e->getMessage();
    }
   

   
}
    /**
     * Create the banker account
     */

     public function registerBanker(Request $request,$id_manager){

       try{

        DB::beginTransaction();

        //Validation of data for User
        $rules = [
            'email' => 'required|email',
            'password' => 'required',

        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
           return response()->json(['message' => $validator->errors()->all()], 400);
        }

        //user Traitement 
        $user=new User();
        $user->email=$request->input('email');
        $user->password=app('hash')->make($request->input('password'));
        $user->role = 1;  // role =  1 for the banker
        $user->save();



         //Valdiation Of data Banker
         $rulesBanker = [
            'nom' => 'required',   //regex:[A-Za-z1-9 ] prevents the user from inputting any special characters
            'prenom' => 'required',
            
        ];

        $data=$request->json()->all();
        $validator = Validator::make($data, $rulesBanker);
        if (!$validator->passes()) {
            return response()->json(['message' => $validator->errors()->all()], 400);
        }
        //Banker Traitement 
        $banker=new Banquier();
        $banker->nom=strip_tags($request->input('nom'));
        $banker->prenom=strip_tags($request->input('prenom'));
        $banker->id=$user->id;
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
