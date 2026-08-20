<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            // Cabang JABODETABEK & Head Office
            ['code' => 'HOTNG', 'name' => 'HEAD OFFICE TANGERANG'],
            ['code' => 'MFLW',  'name' => 'MAISON FEERIE LIVING WORLD'],
            ['code' => 'MFBX',  'name' => 'MAISON FEERIE BINTARO EXCHANGE'],
            ['code' => 'MFCP',  'name' => 'MAISON FEERIE CENTRAL PARK'],
            ['code' => 'MFLMN', 'name' => 'MAISON FEERIE LIPPO MALL NUSANTARA'],
            ['code' => 'MFWCT', 'name' => 'MAISON FEERIE WORLD CAPITAL TOWER'],
            ['code' => 'MFBDK', 'name' => 'MAISON FEERIE BIDAKARA 2'],
            ['code' => 'MFKCH', 'name' => 'MAISON FEERIE KERETA CEPAT HALIM'],
            ['code' => 'MFPMB', 'name' => 'MAISON FEERIE PAKUWON MALL BEKASI'],
            ['code' => 'MFSMB', 'name' => 'MAISON FEERIE SUMMARECON MALL BEKASI'],

            // Cabang SURABAYA (SBY) Terbaru
            ['code' => 'MFGM3', 'name' => 'MAISON FEERIE GALAXY MALL 3 SBY'],
            ['code' => 'MFHDH', 'name' => 'MAISON FEERIE HOKKY FRUIT DARMO HARAPAN SBY'],
            ['code' => 'MFHGF', 'name' => 'MAISON FEERIE HOKKY FRUIT GRAHA FAMILY SBY'],
            ['code' => 'MFHMR', 'name' => 'MAISON FEERIE HOKKY FRUIT MERR SBY'],
            ['code' => 'MFLPS', 'name' => 'MAISON FEERIE LIPPO PLAZA SIDOARJO SBY'],
            ['code' => 'MFPCM', 'name' => 'MAISON FEERIE PAKUWON CITY MALL SBY'],
            ['code' => 'MFPWM', 'name' => 'MAISON FEERIE PAKUWON MALL SBY'],
            ['code' => 'MFSPI', 'name' => 'MAISON FEERIE SUPERMALL PAKUWON INDAH SBY'],
            ['code' => 'MFTJP', 'name' => 'MAISON FEERIE TUNJUNGAN PLAZA SBY'],
            ['code' => 'MFSIL', 'name' => 'MAISON FEERIE SILOAM SBY'],
        ];

        foreach ($branches as $branch) {
            DB::table('branches')->updateOrInsert(
                ['code' => $branch['code']],
                [
                    'name'       => $branch['name'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}