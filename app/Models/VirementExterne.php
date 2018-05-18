<?php
/**
 * Created by PhpStorm.
 * User: Fatizo
 * Date: 14/05/2018
 * Time: 12:33
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VirementExterne extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'num_acc',
        'code_bnk',
        'code_curr',
        'num_acc_ext',
        'code_bnk_ext',
        'code_curr_ext',
        'name_ext',
        'amount_vir',
        'status',
        'sens',
        'url_xml',
        'id_commission',
        'amount_commission'
    ];

    protected $hidden =[

    ];



}