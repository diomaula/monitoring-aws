<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\DataAws;

class AwsLaporanSeeder extends Seeder
{
    public function run(): void
    {
        $awsList = [1, 2, 3];

        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd   = Carbon::now()->endOfDay();

        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd   = Carbon::now()->subMonth()->endOfMonth();

        $this->generateDataForRange($awsList, $previousMonthStart, $previousMonthEnd);
        $this->generateDataForRange($awsList, $currentMonthStart, $currentMonthEnd);
    }

    private function generateDataForRange($awsList, $startDate, $endDate)
    {
        DB::table('data_aws')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->delete();

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

                $rainfall = rand(0, 200) / 10;       // 0-20 mm
                $windDirection = rand(0, 360);       // 0-360 derajat

                DataAws::create([
                    'aws_id'         => $awsId,
                    'timestamp'      => $date,
                    'temperature'    => rand(250, 330) / 10,
                    'humidity'       => rand(650, 950) / 10,
                    'pressure'       => rand(9950, 10150) / 10,
                    'rainfall'       => $rainfall,
                    'wind_speed'     => rand(5, 150) / 10,
                    'wind_direction' => $windDirection,
                    'pancitemp'      => rand(250, 350) / 10,
                    'pancilevel'     => rand(100, 1000) / 10,
                    'solrad'         => rand(100, 1200),
                    'watertemp'      => rand(240, 300) / 10,
                    'waterlevel'     => rand(50, 200) / 10,
                ]);

                $date->addHour();
            }
        }
    }
}
