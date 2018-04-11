<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 28-03-2018
 * Time: 15:51
 */

namespace App\Http\Controllers;

use App\Services\BanksServices;

class BanksController extends Controller
{
    /**
     * get banks list
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function index(){
        $bankService = new BanksServices();
        $banks = $bankService->findAll();
        return $banks;
    }

}