<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use App\Mail\AuthConfirmationMail;
use Illuminate\Support\Facades\Mail;

$app->get('/', function () use ($app) {
    //return $app->version();
   // return response(config('nexmo.tharwa_phone'));
    return '<h1>Tharwa bank ... powered by SOLIDTeam 2018 ^^</h1>';
});



//get list of bankers
$app->get('bankers',['uses' => 'BankersController@index' , 'middleware' => 'auth']);
//get a manager by id
$app->get('managers/{id}',['uses' => 'ManagersController@show','middleware' => ['auth','role:manager']]);
//route to subscribe a customer
$app->post('customers',['uses' => 'RegistersController@registerCustomer']);
//route to subscribe a banker
$app->post('bankers/{id_manager}',['uses' => 'RegistersController@registerBanker']);

$app->get('currency',['uses'=>'CurrenciesController@getExchangeRate']);



