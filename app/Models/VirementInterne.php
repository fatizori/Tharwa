<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 12-04-2018
 * Time: 10:28
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VirementInterne extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
              'num_acc_sender',
              'code_bnk_sender',
              'code_curr_sender',
              'num_acc_receiver',
              'code_bnk_receiver',
              'code_curr_receiver',
              'montant_virement',
              'status',
              'type',
              'id_commission',
              'montant_commission'
    ];

    protected $hidden =[
      'updated_at'
    ];


    public function account_sender()
    {
        return $this->belongsTo(Account::class,'num_acc_sender','id');
    }

    public function account_receiver()
    {
        return $this->belongsTo(Account::class,'num_acc_receiver','id');
    }

}