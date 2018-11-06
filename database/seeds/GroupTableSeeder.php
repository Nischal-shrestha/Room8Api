<?php

use Illuminate\Database\Seeder;

class GroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //make 1 to 5 groups for each house
        App\House::all()->each(function ($house) {
            factory(App\Group::class, rand(1, 5))->create();
        });
    }
}
