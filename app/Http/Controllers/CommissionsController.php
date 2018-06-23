<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 15-04-2018
 * Time: 19:04
 */

namespace App\Http\Controllers;
use App\Models\Account;
use App\Models\MensuelleCommission;
use App\Services\AccountsServices;
use App\Services\BanksServices;
use App\Services\CommissionsServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CommissionsController extends Controller
{
    private $commissionService ;

    /**
     * CommissionsController constructor.
     */
    public function __construct()
    {
        $this->commissionService = new CommissionsServices();
    }

    /**
     * Get Commissions's list
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $commissions = $this->commissionService->findAll();
        return response()->json( $commissions, 200);
    }

    /**
     * Find a commission bye code
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function findByCode($code){
        $commission = $this->commissionService->findCommissionByCode($code);
        if(!$commission){
            return response()->json(['message' => "The commission  doesn't exist"], 404);
        }
        return response()->json($commission, 200);
    }

    /**
     * Find a commission by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){
        $commission = $this->commissionService->findById($id);
        if(!$commission){
            return response()->json(['message' => "The commission with {$id} doesn't exist"], 404);
        }
        return response()->json($commission, 200);
    }

    /**
     * Add a new commission
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function store(Request $request){
        $this->validateRequest($request);
        $data =  $request->json()->all();
        $commission = $this->commissionService->create($data);
       return response(json_encode(['message' => 'new commission  has been added']),201);
    }


    /**
     * Update a commission's data
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        //find the commission by id
        $commission = $this->commissionService->findById($id);
        //if the commission not exist
        if(!$commission){
            return response()->json(['message' => "The commission with {$id} doesn't exist"], 404);
        }
        //if the commission exist validate the data
        $this->validateRequest($request);
        $data = $request->json()->all();
        //update the commission data
         $this->commissionService->update($commission,$data);

        return response()->json(['message' => "The commission with  id {$commission->id} has been updated"], 200);
    }

    /**
     * Delete  a commission
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        //find the commission by id
        $commission= $this->commissionService->findById($id);
        if(!$commission){
            return response()->json(['message' => "The commission with {$id} doesn't exist"], 404);
        }
        //delete the commission
       $this->commissionService->delete($commission,$id);
        return response()->json(['message' =>"The commission with  id {$id} has been deleted"], 200);
    }

    /**
     * Data validation
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
     public function validateRequest(Request $request){
        $rules = [
            'description' => 'required',
            'code' => 'required',
            'type'=> 'required',
            'valeur'=> 'required'
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            return  response()->json(['message' => $validator->errors()->all()], 400);

        }
    }


}