<?php

use Illuminate\Database\Seeder;
use App\House;
use App\User;

class HouseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(House::class, 30)->create();
    }
}
