<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'owner@smartdining.com'],
            [
                'name'     => 'Owner',
                'password' => bcrypt('password'),
                'role'     => 'owner',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@smartdining.com'],
            [
                'name'     => 'Kasir',
                'password' => bcrypt('password'),
                'role'     => 'cashier',
            ]
        );
    }
}
