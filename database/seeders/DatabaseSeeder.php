<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jalankan Seeder Permission terlebih dahulu
        $this->call([
            PermissionSeeder::class,
        ]);

        // 2. Buat User Default beserta Cabang / Outlet-nya
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'        => 'Administrator',
                'password'    => Hash::make('admin123'),
                'role'        => 'ADMIN',
                'branch_code' => 'HOTNG',
                'branch_name' => 'HEAD OFFICE TANGERANG',
            ]
        );

        User::updateOrCreate(
            ['username' => 'it'],
            [
                'name'        => 'IT Support',
                'password'    => Hash::make('it123'),
                'role'        => 'IT',
                'branch_code' => 'HOTNG',
                'branch_name' => 'HEAD OFFICE TANGERANG',
            ]
        );

        User::updateOrCreate(
            ['username' => 'maintenance'],
            [
                'name'        => 'Team Maintenance',
                'password'    => Hash::make('maint123'),
                'role'        => 'MAINTENANCE',
                'branch_code' => 'HOTNG',
                'branch_name' => 'HEAD OFFICE TANGERANG',
            ]
        );

        User::updateOrCreate(
            ['username' => 'mflw'],
            [
                'name'        => 'Maison Feerie Living World',
                'password'    => Hash::make('123123'),
                'role'        => 'OUTLET',
                'branch_code' => 'MFLW',
                'branch_name' => 'MAISON FEERIE LIVING WORLD',
            ]
        );
        User::updateOrCreate(
            ['username' => 'mfbx'],
            [
                'name'        => 'Maison Feerie Bintaro Exchange',
                'password'    => Hash::make('123123'),
                'role'        => 'OUTLET',
                'branch_code' => 'MFBX',
                'branch_name' => 'MAISON FEERIE BINTARO EXCHANGE',
            ]
        );
        User::updateOrCreate(
            ['username' => 'mfcp'],
            [
                'name'        => 'Maison Feerie Central Park',
                'password'    => Hash::make('123123'),
                'role'        => 'OUTLET',
                'branch_code' => 'MFCP',
                'branch_name' => 'MAISON FEERIE CENTRAL PARK',
            ]
        );
        User::updateOrCreate(
            ['username' => 'mflmn'],
            [
                'name'        => 'Maison Feerie Lippo Mall Nusantara',
                'password'    => Hash::make('123123'),
                'role'        => 'OUTLET',
                'branch_code' => 'MFLMN',
                'branch_name' => 'MAISON FEERIE LIPPO MALL NUSANTARA',
            ]
        );
        User::updateOrCreate(
            ['username' => 'mfwct'],
            [
                'name'        => 'Maison Feerie World Capital Tower',
                'password'    => Hash::make('123123'),
                'role'        => 'OUTLET',
                'branch_code' => 'MFWCT',
                'branch_name' => 'MAISON FEERIE WORLD CAPITAL TOWER',
            ]
        );
        User::updateOrCreate(
            ['username' => 'mfbdk'],
            [
                'name'        => 'Maison Feerie Bidakara 2',
                'password'    => Hash::make('123123'),
                'role'        => 'OUTLET',
                'branch_code' => 'MFBDK',
                'branch_name' => 'MAISON FEERIE BIDAKARA 2',
            ]
        );
        User::updateOrCreate(
            ['username' => 'mfkcic'],
            [
                'name'        => 'Maison Feerie Kereta Cepat Halim',
                'password'    => Hash::make('123123'),
                'role'        => 'OUTLET',
                'branch_code' => 'MFKCIC',
                'branch_name' => 'MAISON FEERIE KERETA CEPAT HALIM',
            ]
        );
        User::updateOrCreate(
            ['username' => 'mfpmb'],
            [
                'name'        => 'Maison Feerie Pakuwon Mall Bekasi',
                'password'    => Hash::make('123123'),
                'role'        => 'OUTLET',
                'branch_code' => 'MFPMB',
                'branch_name' => 'MAISON FEERIE PAKUWON MALL BEKASI',
            ]
        );
        User::updateOrCreate(
            ['username' => 'mfsmb'],
            [
                'name'        => 'Maison Feerie Summarecon Mall Bekasi',
                'password'    => Hash::make('123123'),
                'role'        => 'OUTLET',
                'branch_code' => 'MFSMB',
                'branch_name' => 'MAISON FEERIE SUMMARECON MALL BEKASI',
            ]
        );
    }
}