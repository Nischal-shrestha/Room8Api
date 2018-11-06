<?php

use Illuminate\Database\Seeder;

use App\House;
use App\User;
use App\Group;

class GroupUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        House::all()->each(function ($house) {
            $house->groups->each(function ($group) use ($house) {
                $max = count($house->users);
                if ($max) {
                    $rand = rand(1, $max);
                    for ($i = 0; $i < $rand; $i++) {
                        $group->users()->attach($house->users->random()->id);
                    }
                }

            });
        });

        // House::all()->each(function ($house) {
        //     $house->groups()->each(function ($group) use ($house) {
        //         $group->save($house->users->random()->toArray());
        //     });
        //     //$house->groups()->save($house->users->random());
        // });

        // //get all the groups and for each group
        // Group::all()->each(function ($group) {
        //     //attach a random user where user.houses.id == group.house.id 
        //     $uid = $group->house()->users->random()->id;
        //     $group->users()->attach();
        //     // $group->users->attach(User::whereHas('houses', function ($query) {
        //     //     $query->where('id', '=', rand(1,));
        //     // })->random()->id);
        // });
    }
}
