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

$factory->define(App\Entity\Lot::class, function (Faker $faker) {
    $open = $faker->randomNumber(6);
    return [
        'currency_id' => 1,
        'seller_id' => 2,
        'date_time_open' =>$open,
        'date_time_close' => $open+3600,
        'price' => $faker->randomFloat(2,0,100000),
    ];
});
