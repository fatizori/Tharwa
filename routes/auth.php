<?php

$app->post('register',['uses' => 'RegistersController@register']);
$app->post('login', 'LoginsController@login');
$app->post('logout', ['middleware' => 'auth',
    'uses' => 'LoginsController@logout']);
