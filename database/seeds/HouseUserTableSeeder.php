<?php

use Illuminate\Database\Seeder;
use App\User;
use App\House;

class HouseUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->each(function ($user) {
            $user->houses()->attach(
                House::all()->random()->id
            );
        });
    }
}
