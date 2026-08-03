<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Menambahkan kolom attachment pada tabel tickets jika belum ada
        if (Schema::hasTable('tickets') && !Schema::hasColumn('tickets', 'attachment')) {
            Schema::table('tickets', function (Blueprint $table) {
                if (Schema::hasColumn('tickets', 'description')) {
                    $table->string('attachment')->nullable()->after('description');
                } else {
                    $table->string('attachment')->nullable();
                }
            });
        }

        // 2. Menambahkan kolom attachment pada tabel basts jika belum ada
        if (Schema::hasTable('basts') && !Schema::hasColumn('basts', 'attachment')) {
            Schema::table('basts', function (Blueprint $table) {
                if (Schema::hasColumn('basts', 'parts_replaced')) {
                    $table->string('attachment')->nullable()->after('parts_replaced');
                } else {
                    $table->string('attachment')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Drop kolom attachment pada tabel tickets jika ada
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'attachment')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('attachment');
            });
        }

        // Drop kolom attachment pada tabel basts jika ada
        if (Schema::hasTable('basts') && Schema::hasColumn('basts', 'attachment')) {
            Schema::table('basts', function (Blueprint $table) {
                $table->dropColumn('attachment');
            });
        }
    }
};