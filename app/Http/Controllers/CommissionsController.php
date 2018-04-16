<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 15-04-2018
 * Time: 19:04
 */

namespace App\Http\Controllers;
use App\Services\CommissionsServices;


class CommissionsController extends Controller
{
    private $commissionServices ;

    /**
     * CommissionsController constructor.
     */
    public function __construct()
    {
        $this->commissionServices = new CommissionsServices();
    }

    /**
     * Get Commissions's list
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $commissions = $this->commissionServices->findAll();
        return response()->json( $commissions, 200);
    }
    public function show($code){
        $commission = $this->commissionServices->findCommissionByCode($code);
        if(!$commission){
            return response()->json(['message' => "The user  doesn't exist"], 404);
        }
        return response()->json($commission, 200);
    }

}