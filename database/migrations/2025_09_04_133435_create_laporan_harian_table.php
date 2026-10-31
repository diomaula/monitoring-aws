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
        Schema::create('laporan_harian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aws_id')->constrained('aws')->onDelete('cascade');
            $table->date('date');

            // Temperatur
            $table->float('min_temperature')->nullable();
            $table->float('max_temperature')->nullable();
            $table->float('avg_temperature')->nullable();

            // Kelembapan
            $table->float('min_humidity')->nullable();
            $table->float('max_humidity')->nullable();
            $table->float('avg_humidity')->nullable();

            // Tekanan
            $table->float('min_pressure')->nullable();
            $table->float('max_pressure')->nullable();
            $table->float('avg_pressure')->nullable();

            // Opsional tambahan
            $table->float('total_rainfall')->nullable();
            $table->float('avg_wind_speed')->nullable();
            $table->string('dominant_wind_direction')->nullable();

            $table->timestamps();

            $table->unique(['aws_id', 'date']); // 1 station per hari
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_harian');
    }
};
