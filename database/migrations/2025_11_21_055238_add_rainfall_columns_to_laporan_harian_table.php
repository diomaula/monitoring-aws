<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->decimal('rainfall_max', 8, 2)->after('total_rainfall')->default(0);
            $table->integer('rainy_days')->after('rainfall_max')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->dropColumn(['rainfall_max', 'rainy_days']);
        });
    }
};
