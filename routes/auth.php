<?php



//route to authenticate (1st step send credentials)
//$app->post('login', ['uses' => 'LoginsController@sendCodeLogin',
//    'middleware' => ['force_ssl']]);

$app->post('login', ['uses' => 'LoginsController@askCodeLogin']);

//route to authenticate (2nd step send code)
$app->post('login/code', ['uses' => 'LoginsController@Login']);

//route to get token from refresh token
$app->post('login/refresh', ['uses' => 'LoginsController@refresh']);

//route to logout
$app->post('logout', ['middleware' => 'auth',
    'uses' => 'LoginsController@logout']);
