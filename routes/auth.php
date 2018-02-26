<?php



//route to subscribe (in general like user)
$app->post('register',['uses' => 'RegistersController@register']);
//route to authenticate
$app->post('login', 'LoginsController@login');
//route to logout
$app->post('logout', ['middleware' => 'auth',
    'uses' => 'LoginsController@logout']);
