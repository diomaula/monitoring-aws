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
        Schema::table('data_aws', function (Blueprint $table) {
            $table->double('pancitemp', 8, 2)->nullable()->after('wind_direction');
            $table->double('pancilevel', 8, 2)->nullable()->after('pancitemp');
            $table->double('solrad', 8, 2)->nullable()->after('pancilevel');
            $table->double('watertemp', 8, 2)->nullable()->after('solrad');
            $table->double('waterlevel', 8, 3)->nullable()->after('watertemp'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_aws', function (Blueprint $table) {
            $table->dropColumn([
                'pancitemp',
                'pancilevel',
                'solrad',
                'watertemp',
                'waterlevel'
            ]);
        });
    }
};
