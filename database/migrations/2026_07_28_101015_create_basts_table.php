<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('basts', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Tiket (Wajib & Unik)
            $table->foreignId('ticket_id')->unique()->constrained('tickets')->onDelete('cascade');
            
            // Relasi ke User Teknisi (Nullable agar tidak memicu error 1364 jika kosong)
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Nama Petugas/Teknisi yang Mengerjakan (Disimpan dalam bentuk teks)
            $table->string('technician_name')->nullable();
            
            // Tindakan & Sparepart
            $table->text('action_taken');
            $table->text('parts_replaced')->nullable(); // Menggunakan text agar muat jika sparepart banyak
            
            // Lampiran Bukti Pekerjaan (Path File Foto / PDF BAST)
            $table->string('attachment')->nullable();
            
            // Tanggal & Waktu Selesai
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('basts');
    }
};