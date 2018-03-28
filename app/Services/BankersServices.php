<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 28-03-2018
 * Time: 13:45
 */

namespace App\Services;
use App\Models\Banker;

class BankersServices
{
    /**
     * Find the banker by id
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findById($id){
        $banker = Banker::find($id);
        return $banker;
    }

    /**
     * Create a new banker
     * @param $data
     * @param $user_id
     * @param $id_manager
     * @param $photo
     */
    public function create($data,$user_id,$id_manager,$photo){
        $banker = new Banker();
        $banker->name = strip_tags($data['name']);
        $banker->firstname = strip_tags($data['firstname']);
        $banker->address = strip_tags($data['address']);
        $banker->id = $user_id;
        $banker->photo = $photo;
        $banker->id_creator = $id_manager;  // the id of the manager who create the banker account
        $banker->save();
    }

    /**
     * Update the banker avatar
     * @param $id
     * @param $photo
     */
    public function updatePhoto($id,$photo){
        $this->findById($id)->update(['photo'=>$photo]);
    }

}