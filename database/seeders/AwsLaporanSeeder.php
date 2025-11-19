<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AwsLaporanSeeder extends Seeder
{
    public function run(): void
    {
        $awsList = [1, 2, 3]; // id alat dari tabel aws
        $startDate = Carbon::now()->subDays(7)->startOfDay();
        $endDate = Carbon::now();

        foreach ($awsList as $awsId) {
            $date = $startDate->copy();

            while ($date <= $endDate) {
                // Arah angin dalam derajat (0–359)
                $windDirection = rand(0, 359);

                // Kecepatan angin dalam m/s
                $windSpeedMS = rand(1, 15) + mt_rand(0, 99) / 100;

                DB::table('data_aws')->insert([
                    'aws_id'         => $awsId,
                    'timestamp'      => $date,
                    'temperature'    => rand(24, 34) + mt_rand(0, 99) / 100, // °C
                    'humidity'       => rand(60, 95) + mt_rand(0, 99) / 100, // %
                    'pressure'       => rand(995, 1015) + mt_rand(0, 99) / 100, // hPa
                    'rainfall'       => rand(0, 30) + mt_rand(0, 99) / 100, // mm
                    'wind_speed'     => $windSpeedMS, // m/s
                    'wind_direction' => $windDirection, // derajat (°)
                    'pancitemp'      => rand(25, 35) + mt_rand(0, 99) / 100,
                    'pancilevel'     => rand(10, 100) + mt_rand(0, 99) / 100,
                    'solrad'         => rand(100, 1000) + mt_rand(0, 99) / 100,
                    'watertemp'      => rand(24, 30) + mt_rand(0, 99) / 100,
                    'waterlevel'     => rand(50, 200) / 10,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                $date->addHour(); // interval per 1 jam
            }
        }
    }
}
