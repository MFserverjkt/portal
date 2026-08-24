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

        // 1. Insert / Update Permissions
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

        // 2. Pastikan Role ASSET Terdaftar di Tabel Roles (jika ada tabel roles)
        if (\Schema::hasTable('roles')) {
            $roles = ['ADMIN', 'IT', 'MAINTENANCE', 'OUTLET', 'HC', 'ASSET'];
            
            foreach ($roles as $roleName) {
                DB::table('roles')->updateOrInsert(
                    ['name' => $roleName],
                    [
                        'guard_name' => 'web',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            // 3. Optional: Assign Hak Akses Default untuk Role ASSET (Inventori Asset & Lihat Tiket)
            $assetRoleId = DB::table('roles')->where('name', 'ASSET')->value('id');
            if ($assetRoleId && \Schema::hasTable('role_has_permissions')) {
                $defaultAssetPermSlugs = ['assets.index', 'tickets.index'];
                $permIds = DB::table('permissions')->whereIn('slug', $defaultAssetPermSlugs)->pluck('id');

                foreach ($permIds as $permId) {
                    DB::table('role_has_permissions')->updateOrInsert(
                        [
                            'permission_id' => $permId,
                            'role_id'       => $assetRoleId
                        ]
                    );
                }
            }
        }
    }
}