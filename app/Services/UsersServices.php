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
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

//    /**
//     * @param Request $request
//     * @return user or null
//     */
//        public function findAuthUser(Request $request){
//            return $request->user();
//        }

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
     * Update the user data
     * @param Request $request
     * @return bool
     */
    public function updatePassword(Request $request){
        $data =  $request->json()->all();
        $enteredOldPassword = $data['old_password'];
        $user = $request->user();
        $oldPassword = $user->password;

        if (! Hash::check($enteredOldPassword, $oldPassword)) {
            return false;
        }

        return $user->update(['password'=> app('hash')->make($data['new_password'])]);
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