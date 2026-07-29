<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('asset_name');
            $table->string('category');
            $table->string('brand')->nullable();            // <-- Kolom brand
            $table->string('branch_code', 30);              // <-- Kode cabang
            $table->string('branch_name', 150)->nullable(); // <-- Nama cabang
            $table->date('register_date')->nullable();       // <-- Tanggal beli
            $table->string('status')->default('Bagus / Normal');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};