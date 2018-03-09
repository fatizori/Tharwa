<?php



//route to authenticate (1st step send credentials)
$app->post('login', ['uses' => 'LoginsController@sendCodeLogin']);

//$app->post('login', ['uses' => 'LoginsController@sendCodeLogin']);

//route to authenticate (2nd step send code)
$app->post('login/code', ['uses' => 'LoginsController@Login']);

//route to logout
$app->post('logout', ['middleware' => 'auth',
    'uses' => 'LoginsController@logout']);
