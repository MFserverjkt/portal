<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan 'ASSET' ke dalam opsi ENUM kolom role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('ADMIN', 'IT', 'MAINTENANCE', 'OUTLET', 'HC', 'ASSET') NOT NULL DEFAULT 'OUTLET'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('ADMIN', 'IT', 'MAINTENANCE', 'OUTLET', 'HC') NOT NULL DEFAULT 'OUTLET'");
    }
};