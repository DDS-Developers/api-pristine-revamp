<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\RoleEnum;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class)->create([
            'email' => 'admin@mail.com',
            'role' => RoleEnum::Admin
        ]);

        factory(App\User::class)->create([
            'email' => 'customer@mail.com',
            'role' => RoleEnum::Customer,
            'password' => Hash::make('customer')
        ]);
    }
}
