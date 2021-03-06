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
   // $name = \App\Http\Controllers\FilesController::generateNameImageMinUser(4,'banker_1.png');
   // dd($name);
    return '<h1>Tharwa bank ... powered by SOLIDTeam 2018 ^^</h1>';
});



// Bankers
//get a banker by id
$app->get('bankers/{id:[0-9]+}/{option:[01]}',['uses' => 'BankersController@show','middleware' => ['auth','role:manager,banker']]);

$app->group( ['prefix' => 'accounts',
    'middleware' => ['auth','role:banker']],function () use ($app) {
    // get the list of all accounts
    $app->get('', ['uses' => 'AccountsController@index']);
    // manage the user account
    $app->put('/{id:[0-9]+}',['uses' => 'AccountsController@actionOnAccount']);

});
// get the list of non valide accounts
$app->get('accounts/invalid',['uses' => 'AccountsController@invalidAccounts','middleware' => ['auth','role:banker']]);
// Get Notif number
$app->get('notif',['uses' => 'NotificationsController@getNotifNumber','middleware' => ['auth']]);
//route to update banker personal info
$app->put('bankers',['uses' => 'BankersController@changeInfo','middleware' => ['auth','role:banker']]);
//get the list of invalid virements intern
$app->get('virements/intern/invalid',['uses' => 'VirementInternesController@getInvalidVirement', 'middleware' =>['auth','role:banker']]);
//get the list of invalid virements extern
$app->get('virements/extern/invalid',['uses' => 'VirementExternesController@getInvalidVirement', 'middleware' =>['auth','role:banker']]);
////get the list of virements extern
 $app->get('virements/extern',['uses' => 'VirementExternesController@getVirementExternes', 'middleware' =>['auth','role:banker']]);
// get the unblock demand
$app->get('justif_account/{account_id}',['uses' => 'AccountsController@getUnblockDemandByAccountId', 'middleware' =>['auth','role:banker']]);
// refuse account unblock demand (justif)
$app->put('justif_account/{id_justif_account}',['uses' => 'AccountsController@refuseAccountJustif', 'middleware' =>['auth','role:banker']]);
// get all blocked accounts need to be deblocked
$app->get('accounts/block',['uses' => 'AccountsController@getBlockedAccountsToUnblock', 'middleware' =>['auth','role:banker']]);
// get all unblocked accounts need to be block
$app->get('accounts/unblock',['uses' => 'AccountsController@getUnBlockedAccountsToblock', 'middleware' =>['auth','role:banker']]);





//Managers
//get a manager by id
$app->get('managers/{id:[0-9]+}',['uses' => 'ManagersController@show','middleware' => ['auth','role:manager']]);

$app->group( ['prefix' => 'bankers',
              ],function () use ($app) {
    //get list of bankers
    $app->get('', ['uses' => 'BankersController@index']);
    //route to subscribe a banker
    $app->post('',['uses' => 'RegistersController@registerBanker','middleware' => ['auth','role:manager']]);
    //route to block a banker
    $app->delete('/{id_banker:[0-9]+}',['uses' => 'BankersController@blockBanker'],['middleware' => ['auth','role:manager']]);
});
//get the list of banks
$app->get('banks',['uses' => 'BanksController@index','middleware' => ['auth','role:manager'] ]);
//get the list of banks ids
$app->get('banks/id',['uses' => 'BanksController@indexId','middleware' => ['auth','role:customer'] ]);
//route to add a bank
$app->post('banks',['uses' => 'BanksController@store','middleware' => ['auth','role:manager']]);
//route to change personal info manager
$app->put('managers',['uses' => 'ManagersController@changeInfo','middleware' => ['auth','role:manager']]);
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
//Validate exchange justif
$app->put('virements/intern/justif/{id_justif:[0-9]+}',['uses' => 'VirementInternesController@validateTransfer','middleware' => ['auth','role:banker']]);
//validate justif externe transfer
$app->put('virements/extern/justif/{id_justif:[0-9]+}',['uses' => 'VirementExternesController@validateTransfer','middleware' => ['auth','role:banker']]);
//get Dashboard stat
$app->get('dashboard',['uses' => 'DashboardController@getStat','middleware' => ['auth','role:manager']]);


//Customers
// get init info
$app->get('init',['uses'=>'CustomersController@initInfo', 'middleware' => ['auth','role:customer']]);
// get the exchange rate
$app->get('currency',['uses'=>'CurrenciesController@getExchangeRate']);
// route to subscribe a customer
$app->post('customers',['uses' => 'RegistersController@registerCustomer']);
// get account name
$app->get('accounts/name/{id:[0-9]+}',['uses' => 'AccountsController@getNameAccount', 'middleware' =>['auth','role:customer']]);

//Virements (same customer)
$app->post('virements_internes',['uses' => 'VirementInternesController@transferToAccount', 'middleware' =>['auth','role:customer']]);


//Virements interne Tharwa (two customers)
$app->post('virements_internes_thw',['uses' => 'VirementInternesController@transferToOtherUser', 'middleware' =>['auth','role:customer']]);
//get the list of transactions
$app->get('account/virements/{id_account:[0-9]+}',['uses' => 'AccountsController@getTransactions', 'middleware' =>['auth','role:customer']]);

$app->post('virements_externes',['uses' => 'VirementExternesController@externeTransfer', 'middleware' =>['auth','role:customer']]);

//Add other accounts
$app->post('accounts',['uses' => 'AccountsController@addNewLocalAccount', 'middleware' =>['auth','role:customer']]);
// get info of an account
$app->get('accounts/type/{type:[1-4]}', ['uses' => 'AccountsController@show', 'middleware' =>['auth','role:customer']]);
// To set the FCM token
$app->post('fcm/register',['uses' => 'UsersController@registerFCMToken', 'middleware' =>['auth','role:customer']]);

// Set an unblocking justif
$app->post('justif_account',['uses' => 'AccountsController@addJustifAccount', 'middleware' =>['auth','role:customer']]);


//All users
// update user photo
$app->post('user/photo',['uses' => 'RegistersController@update_avatar']);
// update authenticated user  photo
$app->post('user/photo',['uses' => 'UsersController@updatePhoto', 'middleware' =>['auth']]);

$app->put('user/password',['uses' => 'UsersController@changePassword', 'middleware' => 'auth']);

//$app->post('xml',['uses' => 'VirementExternesController@writeToXml']);

// To test the excution of externes transfers
$app->get('excute',['uses' => 'VirementExternesController@executeTransfer']);