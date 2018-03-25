<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 01-03-2018
 * Time: 12:47
 */

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function __construct()
    {
    }

    public function index(){
        $customers = Customer::all();
        //TODO delete this shit!!
        foreach ($customers as $customer){
            $customer->photo = 'customer1.jpg';
        }
        return response()->json($customers, 200);
    }



    public function show($id){
        $customer = Customer::find($id);
        if(!$customer){
            return response()->json(['message' => "The customer with {$id} doesn't exist"], 404);
        }
        //TODO delete this shit!!
        $customer->photo = 'customer1.jpg';
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



}