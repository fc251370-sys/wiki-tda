<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@yopmail.com',
            'password' => Hash::make('Admin123$'),
        ]);

        $user->assignRole('admin');

        $user = User::create([
            'name' => 'user',
            'email' => 'user@yopmail.com',
            'password' => Hash::make('Admin123$'),
        ]);

        $user->assignRole('user');

        $user = User::create([
            'name' => 'viewer',
            'email' => 'viewer@yopmail.com',
            'password' => Hash::make('Admin123$'),
        ]);

        $user->assignRole('viewer');
    }
}
