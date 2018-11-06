<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Mr. Tester',
            'email' => 'test@room8.com',
            'username' => 'iLive2Test',
            'password' => bcrypt('secret'),
        ]);
        factory(App\User::class, 200)->create();
    }
}