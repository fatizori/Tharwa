<?php
namespace App\Http\Controllers;


use App\Services\DashBoardServices;

class DashboardController extends Controller
{
    private $dashboardServices;

    public function __construct()
    {
        $this->dashboardServices = new DashBoardServices();
    }

    public function getStat(){
        $stat = $this->dashboardServices->getStat();
        if($stat){
            return response()->json($stat, 200);
        }else{
            return response()->json(['message' => 'erreur serveur'], 500);
        }
    }
}