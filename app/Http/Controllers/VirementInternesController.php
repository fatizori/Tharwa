<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 12-04-2018
 * Time: 10:28
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\VirementInternesServices;
use App\Services\BanksServices;
use App\Http\Controllers\CurrenciesController;
use Illuminate\Support\Facades\Validator;

class VirementInternesController extends Controller
{

    private $virementInterneService;
    private $bankService;
    /**
     * VirementInternesController constructor.
     */
    public function __construct()
    {
        $this->virementInterneService = new VirementInternesServices();
        $this->bankService = new BanksServices();
    }

    public function transferToAccount(Request $request){

        //Validation of data
        $rules = [
            'num_acc_sender' => 'required | integer',
            'type_acc_sender' => 'required',
            'code_curr_sender' => 'required',
            'num_acc_receiver'=>'required',
            'type_acc_receiver'=>'required',
            'code_curr_receiver'=>'required',
            'montant_virement'=>'required',
            'type'=>'required | integer | between:0,1',
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {

            return   response()->json(['message' => $validator->errors()->all()], 400);
        }

        $currency = new CurrenciesController();
        $montant = $data['montant_virement'];
        //type
        /* if($data['type_acc_sender']== 2 && $data['code_curr_sender']== 'EUR' ){

         }else if($data['type_acc_receiver']== 2 && $data['code_curr_receiver']== 'EUR' ){
             $montant = $montant * $currency->exchangeRateDinarsEuro();
         }else if($data['type_acc_receiver']== 2 && $data['code_curr_receiver']== 'USD' ){
             $montant = $montant * $currency->exchangeRateDinarsDollars();
         }else if($data['type_acc_sender']== 2 && $data['code_curr_sender']== 'USD' ){
             $montant = $montant * $currency->exchangeRateDollarsDinars();
         }*/

         //commission code
        $codeCommission= '';
        if($data['type_acc_sender'] == 0 && $data['type_acc_receiver'] == 1){
             $codeCommission = 'CVE';
        }else if($data['type_acc_sender'] == 1 && $data['type_acc_receiver'] == 0){
            $codeCommission = 'EVC';
        }else if($data['type_acc_sender'] == 0 && $data['type_acc_receiver'] == 2){
            $codeCommission = 'CVD';
        }else if($data['type_acc_sender'] == 2 && $data['type_acc_receiver'] == 0){
            $codeCommission = 'DVC';
        }

        $this->virementInterneService->create($data,'THW','THW',$codeCommission,0,$montant);
        return response(json_encode(['message' => 'transfer success']),201);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function transferToOtherUser(Request $request){
        //Validation of data
        $rules = [
            'num_acc_receiver'=>'required | integer',
            'montant_virement'=>'required | numeric',
            'type'=>'required | integer | between:0,1',
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            return   response()->json(['message' => $validator->errors()->all()], 400);
        }
        $montant = $data['montant_virement'];
        //commission code
        $user = $request->user();
        return $this->virementInterneService->createBetweenCustomersExchange($user, $data['num_acc_receiver'],$montant,$data['type']);
    }

}
