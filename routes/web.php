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

$app->get('/', function () use ($app) {
    //return $app->version();
    //return '<h1>Tharwa bank ... powered by SOLIDTeam 2018 ^^</h1>';
     $name = \App\Http\Controllers\FilesController::generateNameImageMinUser(11,'avatar_1521241261_211020320022221dad37f07867f023154108238960396d717c809e6506444702dd6ead302d4c84317bc287f9cbf87_6c164f25e17f52198b845214c681c921adf4ae22.jpg');
     echo $name;
});

//Bankers
$app->get('bankers/{id}',['uses' => 'BankersController@show','middleware' => ['auth','role:banker']]);
//get the list of all accounts
$app->get('accounts',['uses' => 'AccountsController@index', 'middleware' => ['auth','role:banker']]);
//get the list of non valide accounts
$app->get('accounts_nv',['uses' => 'AccountsController@invalidAccounts','middleware' => ['auth','role:banker']]);
// validate the user account
$app->put('accounts/{id}',['uses' => 'AccountsController@validateAccount','middleware' => ['auth','role:banker']]);


//Managers
//get a manager by id
$app->get('managers/{id}',['uses' => 'ManagersController@show','middleware' => ['auth','role:manager']]);
//route to subscribe a banker
$app->post('bankers',['uses' => 'RegistersController@registerBanker','middleware' => ['auth','role:manager']]);
//get list of bankers
$app->get('bankers',['uses' => 'BankersController@index' , 'middleware' => 'auth']);
//get a banker by id


//Customers
//get the exchange rate
$app->get('currency',['uses'=>'CurrenciesController@getExchangeRate', 'middleware' => 'auth']);
//route to subscribe a customer
$app->post('customers',['uses' => 'RegistersController@registerCustomer']);

//All users
// update user photo
$app->put('update_photo',['uses' => 'RegistersController@update_avatar']);








