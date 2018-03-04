<?php



//route to authenticate (1st step send credentials)
$app->post('login', 'LoginsController@sendCodeLogin');
//route to authenticate (2nd step send code)
$app->post('login/code', 'LoginsController@login');
//route to logout
$app->post('logout', ['middleware' => 'auth',
    'uses' => 'LoginsController@logout']);
