<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 15-04-2018
 * Time: 19:00
 */

namespace App\Services;

Use App\Models\Commission;

class CommissionsServices
{

    /**
     * Get commissions's list
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findAll(){
        $commissions = Commission::all();
        return $commissions;
    }

    /**
     * Find the commission by type
     * @param $type
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findCommissionByCode($code){
        $commission = Commission::where('code',$code)->first();
        return $commission;

    }

}