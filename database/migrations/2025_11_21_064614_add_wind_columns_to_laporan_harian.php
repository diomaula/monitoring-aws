<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->decimal('wind_speed_min', 8, 2)->after('rainy_days')->default(0);
            $table->decimal('wind_speed_max', 8, 2)->after('wind_speed_min')->default(0);
            $table->decimal('wind_speed_avg', 8, 2)->after('wind_speed_max')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->dropColumn(['wind_speed_min', 'wind_speed_max', 'wind_speed_avg']);
        });
    }
};
