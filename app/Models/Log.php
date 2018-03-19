<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 12-03-2018
 * Time: 8:14
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'email_sub',
        'email_obj',
        'message',
        'status',
        'type',
        'date'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

}