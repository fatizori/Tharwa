<?php
use Illuminate\Http\Request;



//route to authenticate
$app->post('login', 'LoginsController@login');
//route to logout
$app->post('logout', ['middleware' => 'auth',
    'uses' => 'LoginsController@logout']);
