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
        Schema::create('data_aws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aws_id')->constrained('aws')->onDelete('cascade');
            $table->timestamp('timestamp'); // waktu data diambil (UTC)
            $table->float('temperature')->nullable();
            $table->float('humidity')->nullable();
            $table->float('pressure')->nullable();
            $table->float('rainfall')->nullable();
            $table->float('wind_speed')->nullable();
            $table->string('wind_direction')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_aws');
    }
};
