<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('technician_name')->nullable()->after('status');
            $table->text('action_taken')->nullable()->after('technician_name');
            $table->date('target_completion_date')->nullable()->after('action_taken');
            $table->string('work_status')->nullable()->after('target_completion_date');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['technician_name', 'action_taken', 'target_completion_date', 'work_status']);
        });
    }
};