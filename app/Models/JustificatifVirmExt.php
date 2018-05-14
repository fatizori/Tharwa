<?php
/**
 * Created by PhpStorm.
 * User: Fatizo
 * Date: 14/05/2018
 * Time: 14:08
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JustificatifVirmExt extends Model
{
    protected $table = 'justificatif_virm_ext';

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