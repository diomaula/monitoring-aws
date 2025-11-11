<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah enum role menjadi menambahkan 'superadmin'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pegawai', 'admin', 'superadmin') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum semula
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pegawai', 'admin') NOT NULL");
    }
};
