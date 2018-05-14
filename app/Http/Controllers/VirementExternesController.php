<?php
/**
 * Created by PhpStorm.
 * User: Mezerreg
 * Date: 14/05/2018
 * Time: 13:48
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\VirementExternesServices;
use App\Services\AccountsServices;
use  App\Jobs\LogJob;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VirementExternesController extends Controller
{
    private $virementExterneService;
    private $accountService;
    /**
     * VirementInternesController constructor.
     */
    public function __construct()
    {
        $this->virementExterneService = new VirementExternesServices();
        $this->accountService = new AccountsServices();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVirementExternes(){
        $virements = $this->virementExterneService->getVirementExternes();
        return response()->json($virements, 200);
    }

}