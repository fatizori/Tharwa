<?php
/**
 * Created by PhpStorm.
 * User: Fatizo
 * Date: 23/06/2018
 * Time: 19:26
 */


namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class MensuelleCommission extends Model
{
    protected $fillable = [
        'id_account',
        'type',
        'amount'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
}