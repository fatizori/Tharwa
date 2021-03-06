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
use App\Models\User;

class Manager extends Model implements AuthenticatableContract, AuthorizableContract
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
        'name',
        'firstname',
        'photo',
        'address'

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class,'id')->get()->first();
    }

}
