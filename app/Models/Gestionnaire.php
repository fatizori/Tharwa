<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 01-03-2018
 * Time: 8:03
 */

namespace App\Models;

use Storage;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Laravel\Passport\HasApiTokens;

class Gestionnaire extends Model implements AuthenticatableContract, AuthorizableContract
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
        'nom',
        'prenom',
        'num_tel'

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * @param $role
     * @return bool
     */




    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
