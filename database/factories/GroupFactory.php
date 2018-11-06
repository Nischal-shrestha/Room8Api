<?php

use Faker\Generator as Faker;


$factory->define(App\Group::class, function (Faker $faker) {
    return [
        'name' => $faker->userName,
        'description' => $faker->sentence,
        'house_id' => App\House::all()->random()->id
    ];
});
