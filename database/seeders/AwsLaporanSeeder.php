<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AwsLaporanSeeder extends Seeder
{
    public function run(): void
    {
        $awsList = [1, 2, 3]; // ID alat AWS
        $startDate = Carbon::now()->subDays(7)->startOfDay();
        $endDate = Carbon::now();

        // Tentukan hari yang AWS-nya mati
        $specialOffDays = [
            Carbon::today()->format('Y-m-d'),            // hari ini
            Carbon::yesterday()->format('Y-m-d'),        // kemarin
            Carbon::now()->subDays(5)->format('Y-m-d'),  // 5 hari lalu
        ];

        foreach ($awsList as $awsId) {

            // Tentukan jam mati untuk tiap hari
            $offHoursMap = [];
            foreach ($specialOffDays as $day) {
                $totalHoursOff = rand(2, 4); // 2–4 jam mati
                $hours = [];

                for ($i = 0; $i < $totalHoursOff; $i++) {
                    $hours[] = rand(0, 23); // jam acak
                }

                $offHoursMap[$day] = $hours;
            }

            // Loop isi data
            $date = $startDate->copy();

            while ($date <= $endDate) {

                $currentDay = $date->format('Y-m-d');
                $currentHour = (int)$date->format('H');

                $isOff = in_array($currentDay, $specialOffDays)
                         && in_array($currentHour, $offHoursMap[$currentDay]);

                if ($isOff) {
                    // data 0 → AWS mati
                    DB::table('data_aws')->insert([
                        'aws_id'         => $awsId,
                        'timestamp'      => $date,
                        'temperature'    => 0,
                        'humidity'       => 0,
                        'pressure'       => 0,
                        'rainfall'       => 0,
                        'wind_speed'     => 0,
                        'wind_direction' => 0,
                        'pancitemp'      => 0,
                        'pancilevel'     => 0,
                        'solrad'         => 0,
                        'watertemp'      => 0,
                        'waterlevel'     => 0,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                } else {
                    // data normal
                    DB::table('data_aws')->insert([
                        'aws_id'         => $awsId,
                        'timestamp'      => $date,
                        'temperature'    => rand(24, 34) + mt_rand(0, 99) / 100,
                        'humidity'       => rand(60, 95) + mt_rand(0, 99) / 100,
                        'pressure'       => rand(995, 1015) + mt_rand(0, 99) / 100,
                        'rainfall'       => rand(0, 30) + mt_rand(0, 99) / 100,
                        'wind_speed'     => rand(1, 15) + mt_rand(0, 99) / 100,
                        'wind_direction' => rand(0, 359),
                        'pancitemp'      => rand(25, 35) + mt_rand(0, 99) / 100,
                        'pancilevel'     => rand(10, 100) + mt_rand(0, 99) / 100,
                        'solrad'         => rand(100, 1000) + mt_rand(0, 99) / 100,
                        'watertemp'      => rand(24, 30) + mt_rand(0, 99) / 100,
                        'waterlevel'     => rand(50, 200) / 10,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }

                $date->addHour(); // interval per jam
            }
        }
    }
}
