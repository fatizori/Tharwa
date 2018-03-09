<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 01-03-2018
 * Time: 8:04
 */

namespace App\Models;

use Storage;

use Illuminate\Database\Eloquent\Model;

use Laravel\Passport\HasApiTokens;

class Bank extends BaseModel
{
 

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'email',
        'address',
        'social_reason'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];



}
