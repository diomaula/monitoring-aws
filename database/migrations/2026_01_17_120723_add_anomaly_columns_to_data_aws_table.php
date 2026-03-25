<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('data_aws', function (Blueprint $table) {
            $table->string('status_anomali')->nullable()->default('Pending')->after('waterlevel'); 
            $table->float('ai_score', 8, 4)->nullable()->after('status_anomali');
        });
    }

    public function down()
    {
        Schema::table('data_aws', function (Blueprint $table) {
            $table->dropColumn(['status_anomali', 'ai_score']);
        });
    }
};
