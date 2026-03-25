<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataAws;
use App\Models\Aws;
use Carbon\Carbon;

class DataAnomaliSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        DataAws::insert([
            [
                'aws_id' => 1,
                'timestamp' => $now,
                'temperature' => 30,
                'humidity' => 80,
                'pressure' => 1008,
                'rainfall' => 0,
                'wind_speed' => 5,
                'wind_direction' => 120,
                'pancitemp' => 32,
                'pancilevel' => 2,
                'solrad' => 500,
                'watertemp' => 28,
                'waterlevel' => 2,
                'status' => 'ANOMALI',
                'anomaly_score' => -0.65,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'aws_id' => 2,
                'timestamp' => $now,
                'temperature' => 28,
                'humidity' => 75,
                'pressure' => 1012,
                'rainfall' => 0,
                'wind_speed' => 3,
                'wind_direction' => 90,
                'pancitemp' => 29,
                'pancilevel' => 1,
                'solrad' => 450,
                'watertemp' => 27,
                'waterlevel' => 1,
                'status' => 'NORMAL',
                'anomaly_score' => 0.15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'aws_id' => 3,
                'timestamp' => $now,
                'temperature' => 31,
                'humidity' => 85,
                'pressure' => 1005,
                'rainfall' => 5,
                'wind_speed' => 6,
                'wind_direction' => 140,
                'pancitemp' => 33,
                'pancilevel' => 3,
                'solrad' => 300,
                'watertemp' => 29,
                'waterlevel' => 3,
                'status' => 'ANOMALI',
                'anomaly_score' => -0.45,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}