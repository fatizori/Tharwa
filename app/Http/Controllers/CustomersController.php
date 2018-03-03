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
        $clients = Client::all();
        return response()->json($clients, 200);
    }



    public function show($id){
        $client = Client::find($id);
        if(!$client){
            return response()->json(['message' => "The client with {$id} doesn't exist"], 404);
        }
        return response()->json($client, 200);
    }



    public function destroy($id){
        $client = Client::find($id);
        if(!$client){
            return response()->json(['message' => "The client with {$id} doesn't exist"], 404);
        }
        $client->delete();
        return response()->json(['message' =>"The client with  id {$id} has been deleted"], 200);
    }



}