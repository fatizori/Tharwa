<?php
/**
 * Created by PhpStorm.
 * User: Mahfoud
 * Date: 09/03/2018
 * Time: 22:52
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s.u';
    }

    public function fromDateTime($value)
    {
        return substr(parent::fromDateTime($value), 0, -3);
    }
}