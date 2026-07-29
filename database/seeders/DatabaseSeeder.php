<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role'     => 'ADMIN',
        ]);

        User::create([
            'name'     => 'IT Support',
            'username' => 'it',
            'password' => Hash::make('it123'),
            'role'     => 'IT',
        ]);

        User::create([
            'name'     => 'Team Maintenance',
            'username' => 'maintenance',
            'password' => Hash::make('maint123'),
            'role'     => 'MAINTENANCE',
        ]);

        User::create([
            'name'     => 'Outlet Mall',
            'username' => 'outlet1',
            'password' => Hash::make('outlet123'),
            'role'     => 'OUTLET',
        ]);
    }
}