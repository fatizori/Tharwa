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
    return '<h1>Tharwa bank ... powered by SOLIDTeam 2018 ^^</h1>';
});

//Bankers
//get list of bankers
$app->get('bankers',['uses' => 'BankersController@index' , 'middleware' => 'auth']);
//get a banker by id
$app->get('bankers/{id}',['uses' => 'BankersController@show','middleware' => ['auth','role:manager']]);
//route to subscribe a banker
$app->post('bankers',['uses' => 'RegistersController@registerBanker','middleware' => ['auth','role:manager']]);


//Managers
//get a manager by id
$app->get('managers/{id}',['uses' => 'ManagersController@show','middleware' => ['auth','role:manager']]);


//Customers
//get the list of non valide accounts
$app->get('accounts',['uses' => 'AccountsController@index', 'middleware' => 'auth' ]);
//route to subscribe a customer
$app->post('customers',['uses' => 'RegistersController@registerCustomer']);

$app->get('currency',['uses'=>'CurrenciesController@getExchangeRate', 'middleware' => 'auth']);

//All users
// update user photo
$app->put('update_photo',['uses' => 'RegistersController@update_avatar']);


