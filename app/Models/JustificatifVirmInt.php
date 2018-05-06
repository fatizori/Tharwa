<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 23-04-2018
 * Time: 16:39
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JustificatifVirmInt extends Model
{
    protected $table = 'justificatif_virm_int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'url_justif',
        'status',
        'date_action_banker',
        'id_vrm',
        'id_banker'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
}