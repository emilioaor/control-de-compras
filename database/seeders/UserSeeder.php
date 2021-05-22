<?php

namespace Database\Seeders;

use App\Models\User;
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
        $user = new User();
        $user->email = 'emilioaor@gmail.com';
        $user->name = 'Emilio Ochoa';
        $user->password = bcrypt('123456');
        $user->role = User::ROLE_ADMIN;
        $user->save();

        $user = new User();
        $user->email = 'jlzreik@yezzcorp.com';
        $user->name = 'Jose Luis Zreik';
        $user->password = bcrypt('123456');
        $user->role = User::ROLE_ADMIN;
        $user->save();

        if (config('app.env') !== 'production') {

            $roles = array_keys(User::rolesAvailable());

            foreach ($roles as $role) {
                User::factory()->create(compact('role'));
            }
        }
    }
}
