<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 28-03-2018
 * Time: 15:51
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\BanksServices;

class BanksController extends Controller
{

	private $bankService;
    public function __construct()
    {    
        $this->bankService = new BanksServices();
    }

    /**
     * get banks list
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function index(){
        
        $banks = $this->bankService->findAll();
        return response()->json($banks, 200);
    }

    /**
     * Find a bank by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){
        $bank = $this->bankService->findById($id);
        if(!$bank){
            return response()->json(['message' => "The bank with {$id} doesn't exist"], 404);
        }
        return response()->json($bank, 200);
    }


    /**
     * Add a new bank
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function store(Request $request){
        $this->validateRequest($request);
        $data =  $request->json()->all();
        $bank = $this->bankService->create($data);
       return response(json_encode(['message' => 'new bank  has been added']),201);
    }


    /**
     * Update a bank's data
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

        //find the bank by id
        $bank = $this->bankService->findById($id);
        //if the bank not exist
        if(!$bank){
            return response()->json(['message' => "The bank with {$id} doesn't exist"], 404);
        }
        //if the bank exist validate the data
        $this->validateRequest($request);
        $data = $request->json()->all();
        //update the bank data
         $this->bankService->update($bank,$data);

        return response()->json(['message' => "The bank with  id {$bank->id} has been updated"], 200);
    }

    /**
     * Delete a bank
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id){
        //find the bank by id
        $bank= $this->bankService->findById($id);
        if(!$bank){
            return response()->json(['message' => "The bank with {$id} doesn't exist"], 404);
        }
        //delete the bank
       $this->bankService->delete($bank,$id);
        return response()->json(['message' =>"The bank with  id {$id} has been deleted"], 200);
    }

    /**
     * Data validation
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
     public function validateRequest(Request $request){
        $rules = [
            'code' => 'required| string| max:3',
            'email' => 'required| email',
            'name' => 'required',
            'address' => 'required',
            'social_reason'=> 'required'
        ];
        $data=$request->json()->all();
        $validator = Validator::make($data, $rules);
        if (!$validator->passes()) {
            return  response()->json(['message' => $validator->errors()->all()], 400);

        }
    }


}