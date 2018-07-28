<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Entity\Money::class, function (Faker $faker) {
    return [
        'amount' => $faker->randomFloat(2,0,100000),
        'currency_id' => 1,
        'wallet_id' => 1,
    ];
});
