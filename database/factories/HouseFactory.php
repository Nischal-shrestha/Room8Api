<?php

use Faker\Generator as Faker;

$factory->define(App\House::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->sentence,
    ];
});
