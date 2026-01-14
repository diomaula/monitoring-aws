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
        Schema::create('laporan_prediksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aws_id')->constrained('aws')->onDelete('cascade');
            $table->date('date');
            $table->float('min_temperature');
            $table->float('max_temperature');
            $table->float('avg_temperature');
            $table->float('min_humidity');
            $table->float('max_humidity');
            $table->float('avg_humidity');
            $table->float('min_pressure');
            $table->float('max_pressure');
            $table->float('avg_pressure');
            $table->float('total_rainfall');
            $table->float('rainfall_max');
            $table->float('wind_speed_min');
            $table->float('wind_speed_max');
            $table->float('wind_speed_avg');
            $table->string('status'); // Normal / Potensi Gangguan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_prediksi');
    }
};
