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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('waktu_prediksi'); // Kapan prediksi ini akan terjadi
            $table->float('nilai_prediksi');    // Nilai suhunya
            $table->string('tipe_sensor')->default('temperature'); // Jenis datanya
            $table->timestamps(); // Kapan prediksi dibuat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
