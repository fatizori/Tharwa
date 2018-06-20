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
        $rateEuro = $this->exchangeRate(1,'EUR','DZD');             //Euro -> Algeria dinars
        $rateUDollars = $this->exchangeRate(1,'USD','DZD');         //Unied Station Dollars -> Algeria dinars
        $rateTurkey =$this->exchangeRate(1,'TRY','DZD');           //Turkey Lira -> Algeria dinars
        $rateCanada = $this->exchangeRate(1,'CAD','DZD');           //Canadian Dollars -> Algeria dinars
        $rateSaoudi = $this->exchangeRate(1,'SAR','DZD');           //Sauodit Ryal -> Algeria dinars
        $rateChina = $this->exchangeRate(1,'CNY','DZD');            //China Yuan -> Algeria dinars
        $rateMaroc = $this->exchangeRate(1,'MAD','DZD');            //Maroco dinars -> Algeria dinars
        $rateTunisia = $this->exchangeRate(1,'TND','DZD');          //Tunisia dinars -> Algeria dinars
        $rateUK = $this->exchangeRate(1,'GBP','DZD');               //Pound sterling -> Algeria dinars
        $rateEmirat = $this->exchangeRate(1,'AED','DZD');          //Emarat dirham -> Algeria dinars

       // Exchange Rates table
       $tabRates= ['Euro'=> $rateEuro,'USDollars'=>$rateUDollars,'TurkeyLira'=>$rateTurkey
           ,'CanadaDollars'=>$rateCanada,'SaouditRyal'=>$rateSaoudi
           ,'ChinaYuan'=>$rateChina,'MarocDinars'=>$rateMaroc,'TunisiaDinars'=>$rateTunisia
         ,'PoundSterling'=>$rateUK,'EmaratDirham'=>$rateEmirat];

        return response(json_encode($tabRates),200);
   }

    /**
     * @param $amount
     * @param $from
     * @param $to
     * @return null|string|string[]
     */
   public function exchangeRate($amount,$from,$to){
     $result = file_get_contents('https://finance.google.com/bctzjpnsun/converter?a='.$amount.'&from='.$from.'&to='.$to);
    preg_match('#\<span class=bld\>(.+?)\<\/span\>#s', $result, $finalData);
    $result1 = preg_replace("/[^0-9.]/", "", $finalData[1]);
    return $result1;
   }

}
