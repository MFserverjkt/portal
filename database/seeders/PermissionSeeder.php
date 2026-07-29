<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Group USER
            ['name' => 'users.index', 'label' => 'User Management', 'group' => 'USER'],
            ['name' => 'users.roles', 'label' => 'User Role & Hak Akses', 'group' => 'USER'],
            
            // Group ASSET
            ['name' => 'assets.index', 'label' => 'Inventori Asset', 'group' => 'ASSET'],
            
            // Group TIKET
            ['name' => 'tickets.index', 'label' => 'Lihat Daftar Tiket', 'group' => 'TIKET'],
            ['name' => 'tickets.create', 'label' => 'Buat Tiket Baru', 'group' => 'TIKET'],
            ['name' => 'tickets.bast', 'label' => 'Proses BAST Tiket', 'group' => 'TIKET'],
            
            // Group REPORT
            ['name' => 'report.it', 'label' => 'Report Corrective IT', 'group' => 'REPORT'],
            ['name' => 'report.maintenance', 'label' => 'Report Corrective Maintenance', 'group' => 'REPORT'],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $perm['name']],
                ['label' => $perm['label'], 'group' => $perm['group']]
            );
        }
    }
}