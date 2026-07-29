<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // Nama Akses (contoh: Lihat Daftar Tiket)
            $table->string('slug')->unique();  // Unique identifier (contoh: tickets.index)
            $table->string('category');        // Kategori Menu (contoh: Tiket Perbaikan)
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Tabel role_has_permissions
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->string('role');            // ADMIN, IT, MAINTENANCE, OUTLET
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->primary(['role', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('permissions');
    }
};