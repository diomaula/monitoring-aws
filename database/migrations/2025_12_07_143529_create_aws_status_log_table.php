<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('aws_status_log', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('aws_id');
            $table->enum('status', ['mati', 'hidup']);
            $table->timestamp('waktu');
            $table->timestamps();

            // Foreign key
            $table->foreign('aws_id')
                  ->references('id')
                  ->on('aws')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('aws_status_log');
    }
};
