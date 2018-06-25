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
     * Find all banks
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findAllValidIds(){
        $ids = Bank::where('status',1)->select('id')->get()->all();
        return $ids;
    }

    /**
     * Find a bank by id
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findById($id){
        $bank = Bank::where('id','=',$id)->first();
        return $bank;
    }

    /**
     * Creata a new bank
     * @param $data
     */
    public function create($data){
        $bank  = new Bank();
        $bank->id = strip_tags($data['code']);
        $bank->email = strip_tags($data['email']);
        $bank->name = strip_tags($data['name']);
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
        $bank->update(['id'=> $data['code'],'name'=> $data['name'],'email'=> $data['email'], 'address'=> $data['address'], 'social_reason'=> $data['social_reason']]);
        $bank->save();
    }

    public function updateBalance($bank,$amount){
        $bank->update(['balance'=> $amount]);
    }
    /**
     * Delete a bank
     * @param $bank
     */

    public function delete($bank){
        $bank->delete();
    }

}