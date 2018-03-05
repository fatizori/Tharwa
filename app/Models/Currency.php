<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 01-03-2018
 * Time: 8:03
 */

namespace App\Models;

use Storage;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Currency extends Model
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
        'code'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];




}
