<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 12-04-2018
 * Time: 10:59
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'description',
        'code',
        'type',
        'valeur'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
}