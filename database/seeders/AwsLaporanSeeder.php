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
        // Hapus data biar tidak dobel
        DB::table('data_aws')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->delete();

        foreach ($awsList as $awsId) {
            $date = $startDate->copy();

            while ($date <= $endDate) {

                DataAws::create([
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
                ]);

                $date->addHour();
            }
        }
    }
}
