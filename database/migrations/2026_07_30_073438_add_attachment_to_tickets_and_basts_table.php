<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menambahkan kolom attachment pada tabel tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('description');
        });

        // Menambahkan kolom attachment pada tabel basts (agar BAST juga bisa upload foto)
        Schema::table('basts', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('parts_replaced');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });

        Schema::table('basts', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};