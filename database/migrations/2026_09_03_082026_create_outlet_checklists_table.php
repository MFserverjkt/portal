<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('outlet_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('dikerjakan_oleh');
            $table->string('outlet');
            $table->enum('order_type', ['Jadwal Preventif', 'IT Helpdesk']);
            $table->date('tanggal_pekerjaan');
            $table->json('items'); // Menyimpan item checklist, radio pilihan, dan keterangan
            $table->text('ttd_it')->nullable();
            $table->text('ttd_leader')->nullable();
            $table->text('ttd_fa_manager')->nullable();
            $table->string('nama_it')->nullable();
            $table->string('nama_leader')->nullable();
            $table->string('nama_fa_manager')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlet_checklists');
    }
};