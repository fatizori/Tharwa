<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Laravel\Passport\HasApiTokens;
use App\Models\User;

class Banker extends Model implements AuthenticatableContract, AuthorizableContract
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
        'id',
        'name',
        'firstname',
        'address',
        'is_active',
        'id_creator',
        'photo'
    ];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at' , 'updated_at'
    ];
   
    
    public function user()
    {
        return $this->belongsTo(User::class,'id');
    }

    public function accounts(){
        return $this->belongsToMany(Account::class,'accounts_management')
        ->withPivot('operation');
    }
}
