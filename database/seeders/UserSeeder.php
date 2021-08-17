<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::updateOrCreate([
            'name'              => 'Admin',
            'email'             => 'admin@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password'          => bcrypt('111'),
        ]);
        User::updateOrCreate([
            'name'              => 'User',
            'email'             => 'user@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password'          => bcrypt('111'),
        ]);
    }
}
