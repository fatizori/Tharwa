<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 26-03-2018
 * Time: 21:48
 */

namespace App\Services;
use App\Models\User;
use Carbon\Carbon;

class UsersServices
{
    /**
     * Find all users
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findAll(){
        $users = User::all();
        return $users;
    }

    /**
     * Find a user by id
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function findById($id){
        $user = User::find($id);
        return $user;
    }

    /**
     * Create a new User
     * @param $data
     * @param $role
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function create($data,$role){
        $user = User::create([
            'email' => $data['email'],
            'password'=> app('hash')->make($data['password']),
            'phone_number'=>$data['phone_number'],
            'role'=>$role,
            'nonce_auth' => sprintf('%04u', random_int(0,9999)),
            'expire_date_nonce' => Carbon::now()->addHours(1)->toDateTimeString()
        ]);
        return $user;
    }

    /**
     * Update the user data
     * @param $user
     * @param $data
     */
    public function update($user,$data){
        $user->update(['email'=> $data['email'], 'password'=> $data['password']]);
    }


    /**
     * Delete a user
     * @param $user
     * @param $id
     */
    public function delete($user,$id){
        $user->delete();
    }
}