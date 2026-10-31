<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin1234'),
            'role' => 'admin',
        ]);

        $user->assignRole('admin');

        $user = User::create([
            'name' => 'coordinador',
            'email' => 'coordinador@gmail.com',
            'password' => Hash::make('Coordinador1234'),
            'role' => 'coordinador',
        ]);

        $user->assignRole('coordinador');

        $user = User::create([
            'name' => 'teacher',
            'email' => 'teacher@gmail.com',
            'password' => Hash::make('Teacher1234'),
            'role' => 'teacher',
        ]);

        $user->assignRole('teacher');

        $user = User::create([
            'name' => 'student',
            'email' => 'student@gmail.com',
            'password' => Hash::make('Student1234'),
            'role' => 'student',
        ]);

        $user->assignRole('student');
    }
}
