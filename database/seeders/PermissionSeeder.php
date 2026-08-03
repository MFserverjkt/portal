<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Kategori USER
            [
                'name'        => 'User Management',
                'slug'        => 'users.index',
                'category'    => 'USER',
                'description' => 'Mengakses halaman daftar user'
            ],
            [
                'name'        => 'User Role & Hak Akses',
                'slug'        => 'users.roles',
                'category'    => 'USER',
                'description' => 'Mengatur role dan hak akses pengguna'
            ],

            // Kategori ASSET
            [
                'name'        => 'Inventori Asset',
                'slug'        => 'assets.index',
                'category'    => 'ASSET',
                'description' => 'Melihat dan mengelola inventori aset'
            ],

            // Kategori TIKET
            [
                'name'        => 'Lihat Daftar Tiket',
                'slug'        => 'tickets.index',
                'category'    => 'TIKET',
                'description' => 'Melihat daftar seluruh tiket perbaikan'
            ],
            [
                'name'        => 'Buat Tiket Baru',
                'slug'        => 'tickets.create',
                'category'    => 'TIKET',
                'description' => 'Membuat tiket pengajuan perbaikan baru'
            ],
            [
                'name'        => 'Proses BAST Tiket',
                'slug'        => 'tickets.bast',
                'category'    => 'TIKET',
                'description' => 'Memproses Berita Acara Serah Terima (BAST)'
            ],

            // Kategori REPORT
            [
                'name'        => 'Report Corrective IT',
                'slug'        => 'report.it',
                'category'    => 'REPORT',
                'description' => 'Melihat laporan perbaikan divisi IT'
            ],
            [
                'name'        => 'Report Corrective Maintenance',
                'slug'        => 'report.maintenance',
                'category'    => 'REPORT',
                'description' => 'Melihat laporan perbaikan divisi Maintenance'
            ],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $perm['slug']],
                [
                    'name'        => $perm['name'],
                    'category'    => $perm['category'],
                    'description' => $perm['description'],
                    'updated_at'  => now(),
                    'created_at'  => now(),
                ]
            );
        }
    }
}