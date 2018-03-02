<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/
use App\Models\User;
use \Carbon\Carbon;

$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'email' => $faker->unique()->safeEmail,
        'role' => rand(0,1),
        'password' => $password ?: $password = app('hash')->make('password'),
        'nonce_auth' => sprintf('%04u', $faker->numberBetween(0,9999)),
        'expire_date_nonce' => Carbon::now()->addHours(1)->toDateTimeString()
    ];
});
