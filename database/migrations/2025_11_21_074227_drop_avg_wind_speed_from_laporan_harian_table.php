<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->dropColumn('avg_wind_speed');
        });
    }

    public function down()
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->decimal('avg_wind_speed', 8, 2)->default(0.00);
        });
    }
};
