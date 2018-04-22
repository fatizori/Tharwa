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

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name',
        'id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public static function getCurrenciesCodes(){
        return array_column(self::all()->toArray(),'id');
    }

}
