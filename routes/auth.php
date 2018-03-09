<?php



//route to authenticate (1st step send credentials)
$app->post('login', ['uses' => 'LoginsController@sendCodeLogin',
    'middleware' => ['force_ssl']]);

//$app->post('login', ['uses' => 'LoginsController@sendCodeLogin']);

//route to authenticate (2nd step send code)
$app->post('login/code', ['uses' => 'LoginsController@Login',
    'middleware' => ['force_ssl']]);

//route to logout
$app->post('logout', ['middleware' => 'auth',
    'uses' => 'LoginsController@logout']);
