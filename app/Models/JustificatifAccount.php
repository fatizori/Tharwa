<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JustificatifAccount extends Model
{
    protected $table = 'justificatif_account';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
       'object',
        'justification',
        'id_account',
        'id_banker',
    ];
}
