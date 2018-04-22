<?php

namespace App\Models;

use Storage;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Laravel\Passport\HasApiTokens;
use App\Models\Customer;
use App\Models\Banker;

class Account extends Model implements AuthenticatableContract, AuthorizableContract
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
        'currency_code',
        'balance',
        'type',
        'status',
        'id_customer'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];


     public function customer()
    {
        return $this->belongsTo(Customer::class);
    }



    public function bankers(){
        return $this->belongsToMany(Banker::class, 'accounts_management')
            ->withPivot('operation')
            ->withTimestamps('created_at',null);
    }

}
