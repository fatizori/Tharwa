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

class AccountAction extends Model
{


    protected $table = 'accounts_management';
        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */

    protected $fillable = [
        'banker_id',
        'account_id',
        'operation',
        'object',
        'justification'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       'updated_at'
    ];


}
