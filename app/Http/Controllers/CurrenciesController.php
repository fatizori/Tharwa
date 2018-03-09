<?php
/**
 * Created by PhpStorm.
 * User: mezerreg
 * Date: 09-03-2018
 * Time: 10:22
 */

namespace App\Http\Controllers;
use App\Models\Currency;
use Swap\Laravel\Facades\Swap;



class CurrenciesController extends Controller
{

    //****************************    Get Exchange Rate   ******************************//

   public function getExchangeRate(){

       // Get the exchange rate in real time
        $rateEuro = Swap::latest('EUR/DZD');             //Euro -> Algeria dinars
        $rateUDollars = Swap::latest('USD/DZD');         //Unied Station Dollars -> Algeria dinars
        $rateTurkey = Swap::latest('TRY/DZD');           //Turkey Lira -> Algeria dinars
        $rateCanada = Swap::latest('CAD/DZD');           //Canadian Dollars -> Algeria dinars
        $rateSaoudi = Swap::latest('SAR/DZD');           //Sauodit Ryal -> Algeria dinars
        $rateChina = Swap::latest('CNY/DZD');            //China Yuan -> Algeria dinars
        $rateMaroc = Swap::latest('MAD/DZD');            //Maroco dinars -> Algeria dinars
        $rateTunisia = Swap::latest('TND/DZD');          //Tunisia dinars -> Algeria dinars
        $rateUK = Swap::latest('GBP/DZD');               //Pound sterling -> Algeria dinars
        $rateEmirat = Swap::latest('AED/DZD');           //Emarat dirham -> Algeria dinars

       // Exchange Rates table
        $tabRates= [$rateEuro->getValue(),$rateUDollars->getValue(),$rateTurkey->getValue(),$rateCanada->getValue(),$rateSaoudi->getValue()
        ,$rateChina->getValue(),$rateMaroc->getValue(),$rateTunisia->getValue(),$rateUK->getValue(),$rateEmirat->getValue()];

        return response(json_encode($tabRates),200);
   }
}