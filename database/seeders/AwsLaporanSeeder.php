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

        foreach ($awsList as $awsId) {

            $date = $startDate->copy();

            while ($date <= $endDate) {

                // ===========================
                // RAINFALL REALISTIS
                // ===========================
                // 60% kemungkinan tidak hujan
                // 25% hujan ringan
                // 10% hujan sedang
                // 5% hujan deras
                $rnd = rand(1, 100);

                if ($rnd <= 60) {
                    $rainfall = 0.0;
                } elseif ($rnd <= 85) {
                    $rainfall = rand(0, 20) / 10; // 0.0 - 2.0 mm
                } elseif ($rnd <= 95) {
                    $rainfall = rand(20, 100) / 10; // 2.0 - 10.0 mm
                } else {
                    $rainfall = rand(100, 300) / 10; // 10.0 - 30.0 mm
                }

                // ===========================
                // WIND DIRECTION REALISTIS
                // ===========================
                // Dominan Timur Laut – Tenggara
                $windDirectionRanges = [
                    rand(20, 70),  // Timur Laut
                    rand(70, 140), // Timur
                    rand(140, 160), // Tenggara
                ];
                $windDirection = $windDirectionRanges[array_rand($windDirectionRanges)];

                // ===========================
                // INSERT DATA REALISTIS
                // ===========================
                DataAws::create([
                    'aws_id'         => $awsId,
                    'timestamp'      => $date,
                    'temperature'    => rand(250, 330) / 10,   // 25 - 33 C
                    'humidity'       => rand(650, 950) / 10,   // 65 - 95 %
                    'pressure'       => rand(9950, 10150) / 10, // 995 - 1015 hPa
                    'rainfall'       => $rainfall,
                    'wind_speed'     => rand(5, 150) / 10,    // 0.5 - 15 m/s
                    'wind_direction' => $windDirection,
                    'pancitemp'      => rand(250, 350) / 10,
                    'pancilevel'     => rand(100, 1000) / 10,
                    'solrad'         => rand(100, 1200) / 1,   // radiasi matahari
                    'watertemp'      => rand(240, 300) / 10,
                    'waterlevel'     => rand(50, 200) / 10,
                ]);

                $date->addHour();
            }
        }
    }
}
