<?php

namespace App\Models;

use Storage;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Laravel\Passport\HasApiTokens;

class Compte extends Model implements AuthenticatableContract, AuthorizableContract
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
        'code_banque',
        'code_monnaie',
        'balance',
        'type',
        'statut',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];

    


     public function user()
    {
        return $this->belongsTo('App\User');
    }

}
