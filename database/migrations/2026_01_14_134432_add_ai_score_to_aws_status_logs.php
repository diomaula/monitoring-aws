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
        Schema::table('aws_status_logs', function (Blueprint $table) {
            // 1. Tambah kolom 'name' setelah 'aws_id'
            // Gunakan nullable() agar data lama tidak error
            $table->string('name')->nullable()->after('aws_id');

            // 2. Tambah kolom 'ai_score' setelah 'status'
            // Gunakan float karena nilainya desimal (misal: -0.45)
            $table->float('ai_score')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aws_status_logs', function (Blueprint $table) {
            $table->dropColumn(['name', 'ai_score']);
        });
    }
};
