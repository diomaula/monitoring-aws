<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // UBAH NAMA TABEL JADI PLURAL (pake 's')
        Schema::create('aws_status_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('aws_id');
            
            // UBAH ENUM JADI STRING (Agar bisa simpan 'Normal'/'Anomali')
            $table->string('status'); 
            
            // Kolom description bisa langsung ditambahkan di sini saja agar rapi
            $table->text('description')->nullable(); 

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
        Schema::dropIfExists('aws_status_logs');
    }
};