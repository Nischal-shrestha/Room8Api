<?php

use Faker\Generator as Faker;
use App\Transaction;
use App\User;
use App\Group;
use App\House;

$factory->define(Transaction::class, function (Faker $faker) {

    $is_set = false;
    while (!$is_set) {
        $g_set = $u_set = false;

        $house = House::all()->random();
        if (count($house->groups)) {
            $group = $house->groups->random();
            $g_set = true;

            if (count($group->users)) {
                $user = $group->users->random();
                $u_set = true;
            }
        }
        if ($g_set && $u_set) {
            $is_set = true;
        }
    }

    return [
        'description' => $faker->sentence(),
        'amount' => $faker->numberBetween(3, 99),
        'location' => $faker->company . " " . $faker->streetName,
        'user_id' => $user->id,
        'group_id' => $group->id,
        'house_id' => $house->id
    ];
});
