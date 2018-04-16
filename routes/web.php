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
    return '<h1>Tharwa bank ... powered by SOLIDTeam 2018 ^^</h1>';
});

// Bankers
//get a banker by id
$app->get('bankers/{id}',['uses' => 'BankersController@show','middleware' => ['auth','role:banker']]);
// get the list of all accounts
$app->get('accounts',['uses' => 'AccountsController@index', 'middleware' => ['auth','role:banker']]);
// get the list of non valide accounts
$app->get('accounts_nv',['uses' => 'AccountsController@invalidAccounts','middleware' => ['auth','role:banker']]);
// validate the user account
$app->put('accounts/{id}',['uses' => 'AccountsController@validateAccount','middleware' => ['auth','role:banker']]);
// Get Notif number
$app->get('notif',['uses' => 'NotificationsController@getNotifNumber','middleware' => ['auth']]);
//route to update banker personal info
$app->put('bankers',['uses' => 'BankersController@changeInfo','middleware' => ['auth','role:banker']]);

//Managers
//get a manager by id
$app->get('managers/{id}',['uses' => 'ManagersController@show','middleware' => ['auth','role:manager']]);
$app->group( ['prefix' => 'bankers',
              'middleware' => ['auth','role:manager']],function () use ($app) {
    //get list of bankers
    $app->get('', ['uses' => 'BankersController@index']);
    //route to subscribe a banker
    $app->post('',['uses' => 'RegistersController@registerBanker']);
    //route to block a banker
    $app->put('/block/{id_banker:[0-9]+}',['uses' => 'BankersController@blockBanker']);
});

//get the list of banks
$app->get('banks',['uses' => 'BanksController@index','middleware' => ['auth','role:manager'] ]);
//route to add a bank
$app->post('banks',['uses' => 'BanksController@store','middleware' => ['auth','role:manager']]);
//route to update a bank's data
$app->put('banks/{id}',['uses' => 'BanksController@update','middleware' => ['auth','role:manager']]);
//route to delete a bank
$app->delete('banks/{id}',['uses' => 'BanksController@destroy','middleware' => ['auth','role:manager']]);
//get the list of commissions
$app->get('commissions',['uses' => 'CommissionsController@index']);
//route to add a commission
$app->post('commissions',['uses' => 'CommissionsController@store','middleware' => ['auth','role:manager']]);
//route to update a commission's data
$app->put('commissions/{id}',['uses' => 'CommissionsController@update','middleware' => ['auth','role:manager']]);
//route to delete a commission
$app->delete('commissions/{id}',['uses' => 'CommissionsController@destroy','middleware' => ['auth','role:manager']]);
//route to update banker personal info
$app->put('bankers',['uses' => 'BankersController@changeInfo','middleware' => ['auth','role:banker']]);
//block banker


//Customers
//get the exchange rate
$app->get('currency',['uses'=>'CurrenciesController@getExchangeRate']);
//route to subscribe a customer
$app->post('customers',['uses' => 'RegistersController@registerCustomer']);



//All users
// update user photo
$app->put('update_photo',['uses' => 'RegistersController@update_avatar']);
$app->put('change_password',['uses' => 'UsersController@changePassword', 'middleware' => 'auth']);

//Virements
$app->post('virements_internes',['uses' => 'VirementInternesController@transferToAccount']);







