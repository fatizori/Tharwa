<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function validateData($data,$rules){
        $validator = Validator::make($data, $rules);
        return $validator->passes();
    }
}
