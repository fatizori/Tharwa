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
     * Find a commission by id
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
     public function findById($id){
        $commission = Commission::where('id',$id)->first();
        return $commission;
    }


    /**
     * Create a new commission
     * @param $data
     */
    public function create($data){
        $commission = new Commission();
        $commission->description = strip_tags($data['description']);
        $commission->code = strip_tags($data['code']);
        $commission->type = strip_tags($data['type']);
        $commission->valeur = strip_tags($data['valeur']);
        $commission->save();
    }


    /**
     * Update a commission's data
     * @param $commission
     * @param $data
     */
    public function update($commission,$data){
        $commission->update(['description'=> $data['description'], 'code'=> $data['code']
            , 'type'=> $data['type'], 'valeur'=> $data['valeur']]);
    }


    /**
     * Delete a commission
     * @param $commission
     * @param $id
     */
    public function delete($commission,$id){
        $commission->delete();
    }

}