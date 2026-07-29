<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            // Pembeda Divisi Perbaikan IT / MAINTENANCE
            $table->enum('department', ['IT', 'MAINTENANCE']);
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['Rendah', 'Sedang', 'Tinggi']);
            $table->enum('status', ['Terbuka', 'Selesai'])->default('Terbuka');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};