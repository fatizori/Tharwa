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

    /**
     * Find a bank by id
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findById($id){
        $bank = Bank::find($id);
        return $bank;
    }

    /**
     * Creata a new bank
     * @param $data
     */
    public function create($data){
        $bank  = new Bank();
        $bank->email = strip_tags($data['email']);
        $bank->address = strip_tags($data['address']);
        $bank->social_reason = strip_tags($data['social_reason']);
        $bank->save();
    }

    /**
     * Update a bank's data
     * @param $bank
     * @param $data
     */
    public function update($bank,$data){
        $bank->update(['email'=> $data['email'], 'address'=> $data['address'], 'social_reason'=> $data['social_reason']]);
    }


    /**
     * Delete a bank
     * @param $bank
     * @param $id
     */

    public function delete($bank,$id){
        $bank->delete();
    }

}