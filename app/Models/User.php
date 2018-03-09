<?php

namespace App\Models;

use Storage;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Laravel\Passport\HasApiTokens;

class User extends BaseModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable,
        Authorizable,
        HasApiTokens;

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */

    protected $fillable = [
        'email',
        'password',
        'phone_number',
        'role',
        'nonce_auth',
        'expire_date_nonce'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @param $role
     * @return bool
     */
    public function hasRole($role){
        if ($role === $this->getRole()){
            return true;
        }else{
            return false;
        }
    }

    public function getRole(){
        $rolesList=config('oauth.roles');
        switch ($this->role){
            case 0: return $rolesList['customer'];
            case 1: return $rolesList['banker'];
            case 2: return $rolesList['manager'];
            default: return '*';    //TODO All the users must have roles?
        }
    }

    public function account()
    {
        return $this->hasMany('App\Account');
    }
    public function customer()
    {
        return $this->hasOne('App\Customer');
    }
    public function banker()
    {
        return $this->hasOne('App\Banker');
    }
    public function manager()
    {
        return $this->hasOne('App\Manager');
    }

}
