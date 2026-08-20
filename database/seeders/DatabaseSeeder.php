<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jalankan Seeder Tambahan (Permission & Branch)
        $this->call([
            PermissionSeeder::class,
            BranchSeeder::class,
        ]);

        // 2. Daftar User Default System & Outlet
        $users = [
            // Internal & Support
            [
                'username'    => 'admin',
                'name'        => 'Administrator',
                'password'    => 'admin123',
                'role'        => 'ADMIN',
                'branch_code' => 'HOTNG',
                'branch_name' => 'HEAD OFFICE TANGERANG',
            ],
            [
                'username'    => 'it',
                'name'        => 'IT Support',
                'password'    => 'it123',
                'role'        => 'IT',
                'branch_code' => 'HOTNG',
                'branch_name' => 'HEAD OFFICE TANGERANG',
            ],
            [
                'username'    => 'maintenance',
                'name'        => 'Team Maintenance',
                'password'    => 'maint123',
                'role'        => 'MAINTENANCE',
                'branch_code' => 'HOTNG',
                'branch_name' => 'HEAD OFFICE TANGERANG',
            ],

            // Outlet JABODETABEK
            [
                'username'    => 'mflw',
                'name'        => 'Maison Feerie Living World',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFLW',
                'branch_name' => 'MAISON FEERIE LIVING WORLD',
            ],
            [
                'username'    => 'mfbx',
                'name'        => 'Maison Feerie Bintaro Exchange',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFBX',
                'branch_name' => 'MAISON FEERIE BINTARO EXCHANGE',
            ],
            [
                'username'    => 'mfcp',
                'name'        => 'Maison Feerie Central Park',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFCP',
                'branch_name' => 'MAISON FEERIE CENTRAL PARK',
            ],
            [
                'username'    => 'mflmn',
                'name'        => 'Maison Feerie Lippo Mall Nusantara',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFLMN',
                'branch_name' => 'MAISON FEERIE LIPPO MALL NUSANTARA',
            ],
            [
                'username'    => 'mfwct',
                'name'        => 'Maison Feerie World Capital Tower',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFWCT',
                'branch_name' => 'MAISON FEERIE WORLD CAPITAL TOWER',
            ],
            [
                'username'    => 'mfbdk',
                'name'        => 'Maison Feerie Bidakara 2',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFBDK',
                'branch_name' => 'MAISON FEERIE BIDAKARA 2',
            ],
            [
                'username'    => 'mfkch',
                'name'        => 'Maison Feerie Kereta Cepat Halim',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFKCH',
                'branch_name' => 'MAISON FEERIE KERETA CEPAT HALIM',
            ],
            [
                'username'    => 'mfpmb',
                'name'        => 'Maison Feerie Pakuwon Mall Bekasi',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFPMB',
                'branch_name' => 'MAISON FEERIE PAKUWON MALL BEKASI',
            ],
            [
                'username'    => 'mfsmb',
                'name'        => 'Maison Feerie Summarecon Mall Bekasi',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFSMB',
                'branch_name' => 'MAISON FEERIE SUMMARECON MALL BEKASI',
            ],

            // Outlet SURABAYA (SBY) Terbaru
            [
                'username'    => 'mfgm3',
                'name'        => 'Maison Feerie Galaxy Mall 3 SBY',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFGM3',
                'branch_name' => 'MAISON FEERIE GALAXY MALL 3 SBY',
            ],
            [
                'username'    => 'mfhdh',
                'name'        => 'Maison Feerie Hokky Fruit Darmo Harapan SBY',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFHDH',
                'branch_name' => 'MAISON FEERIE HOKKY FRUIT DARMO HARAPAN SBY',
            ],
            [
                'username'    => 'mfhgf',
                'name'        => 'Maison Feerie Hokky Fruit Graha Family SBY',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFHGF',
                'branch_name' => 'MAISON FEERIE HOKKY FRUIT GRAHA FAMILY SBY',
            ],
            [
                'username'    => 'mfhmr',
                'name'        => 'Maison Feerie Hokky Fruit Merr SBY',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFHMR',
                'branch_name' => 'MAISON FEERIE HOKKY FRUIT MERR SBY',
            ],
            [
                'username'    => 'mflps',
                'name'        => 'Maison Feerie Lippo Plaza Sidoarjo SBY',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFLPS',
                'branch_name' => 'MAISON FEERIE LIPPO PLAZA SIDOARJO SBY',
            ],
            [
                'username'    => 'mfpcm',
                'name'        => 'Maison Feerie Pakuwon City Mall SBY',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFPCM',
                'branch_name' => 'MAISON FEERIE PAKUWON CITY MALL SBY',
            ],
            [
                'username'    => 'mfpwm',
                'name'        => 'Maison Feerie Pakuwon Mall SBY',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFPWM',
                'branch_name' => 'MAISON FEERIE PAKUWON MALL SBY',
            ],
            [
                'username'    => 'mfspi',
                'name'        => 'Maison Feerie Supermall Pakuwon Indah SBY',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFSPI',
                'branch_name' => 'MAISON FEERIE SUPERMALL PAKUWON INDAH SBY',
            ],
            [
                'username'    => 'mftjp',
                'name'        => 'Maison Feerie Tunjungan Plaza SBY',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFTJP',
                'branch_name' => 'MAISON FEERIE TUNJUNGAN PLAZA SBY',
            ],
            [
                'username'    => 'mfsil',
                'name'        => 'Maison Feerie Siloam SBY',
                'password'    => '123123',
                'role'        => 'OUTLET',
                'branch_code' => 'MFSIL',
                'branch_name' => 'MAISON FEERIE SILOAM SBY',
            ],
        ];

        // 3. Eksekusi Seeder User
        foreach ($users as $userData) {
            User::updateOrCreate(
                ['username' => $userData['username']],
                [
                    'name'        => $userData['name'],
                    'password'    => Hash::make($userData['password']),
                    'role'        => $userData['role'],
                    'branch_code' => $userData['branch_code'],
                    'branch_name' => $userData['branch_name'],
                ]
            );
        }
    }
}