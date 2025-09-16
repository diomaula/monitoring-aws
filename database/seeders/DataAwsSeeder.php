<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataAws;
use Carbon\Carbon;

class DataAwsSeeder extends Seeder
{
    public function run()
    {
        $awsIds = [1, 2, 3]; // misalnya ada 3 alat AWS

        foreach ($awsIds as $awsId) {
            $start = Carbon::now()->subDays(7)->startOfDay();

            // loop setiap 3 jam selama 7 hari
            for ($i = 0; $i < (7 * 8); $i++) {
                $timestamp = $start->copy()->addHours($i * 3);

                DataAws::create([
                    'aws_id'        => $awsId,
                    'timestamp'     => $timestamp,
                    'temperature'   => rand(20, 35),  // °C
                    'humidity'      => rand(60, 90),  // %
                    'pressure'      => rand(1000, 1020), // hPa
                    'rainfall'      => rand(0, 50),   // mm
                    'wind_speed'    => rand(0, 15),   // m/s
                    'wind_direction'=> rand(0, 360),  // derajat
                    'pancitemp'     => rand(20, 30),
                    'pancilevel'    => rand(0, 100),
                    'solrad'        => rand(100, 1000),
                    'watertemp'     => rand(20, 28),
                    'waterlevel'    => rand(0, 200),
                ]);
            }
        }
    }
}
