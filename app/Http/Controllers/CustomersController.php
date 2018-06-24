<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use App\Services\CustomersServices;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    private $customerServices;

    public function __construct()
    {
        $this->customerServices = new CustomersServices();
    }

    public function index(){
        $customers = Customer::all();
        return response()->json($customers, 200);
    }



    public function show($id){
        $customer = Customer::find($id);
        if(!$customer){
            return response()->json(['message' => "The customer with {$id} doesn't exist"], 404);
        }
        return response()->json($customer, 200);
    }



    public function destroy($id){
        $customer = Customer::find($id);
        if(!$customer){
            return response()->json(['message' => "The customer with {$id} doesn't exist"], 404);
        }
        try {
            $customer->delete();
        } catch (\Exception $e) {
        }
        return response()->json(['message' =>"The customer with  id {$id} has been deleted"], 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initInfo(Request $request){
        $user = $request->user();
        $response = $this->customerServices->getInitialInfos($user->id);
        $response = array_merge([
            'user_id' => $user->id,
            'user_type' => $user->role
        ],$response);
        return $response;
    }

}