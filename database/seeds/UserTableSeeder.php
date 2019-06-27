<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Anilkumar Kalyane',
            'email' => 'anilk@example.com',
            'password' => app('hash')->make('anil@2019'),
            'remember_token' => str_random(10),
        ]);
    }
}
