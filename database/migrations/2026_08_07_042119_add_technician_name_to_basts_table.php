<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('basts', function (Blueprint $table) {
            // Cek dulu apakah kolom technician_name belum ada
            if (!Schema::hasColumn('basts', 'technician_name')) {
                $table->string('technician_name')->nullable()->after('technician_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('basts', function (Blueprint $table) {
            if (Schema::hasColumn('basts', 'technician_name')) {
                $table->dropColumn('technician_name');
            }
        });
    }
};