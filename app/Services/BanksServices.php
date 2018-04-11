<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 28-03-2018
 * Time: 15:53
 */

namespace App\Services;
use App\Models\Bank;

class BanksServices
{
    /**
     * Find all banks
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findAll(){
        $banks = Bank::all();
        return $banks;
    }

}