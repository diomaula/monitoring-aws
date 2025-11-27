<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanHarian;
use App\Models\Aws;
use Carbon\Carbon;

class LaporanHarianSeeder extends Seeder
{
    public function run()
    {
        $awsList = Aws::all();

        if ($awsList->count() == 0) {
            $awsList = collect([
                Aws::create(['nama' => 'AWS Default', 'lokasi' => 'Test'])
            ]);
        }

        $windDirections = [
            'N'  => 'Bertiup dari Utara',
            'NE' => 'Bertiup dari Timur Laut',
            'E'  => 'Bertiup dari Timur',
            'SE' => 'Bertiup dari Tenggara',
            'S'  => 'Bertiup dari Selatan',
            'SW' => 'Bertiup dari Barat Daya',
            'W'  => 'Bertiup dari Barat',
            'NW' => 'Bertiup dari Barat Laut',
        ];

        foreach ($awsList as $aws) {
            for ($i = 0; $i < 60; $i++) {
                $tanggal = Carbon::now()->subDays($i)->toDateString();

                $hourlyTemps = [];
                $hourlyHumidity = [];
                $hourlyPressure = [];
                $hourlyWindSpeed = [];
                $hourlyWindDir = [];
                $hourlyRainfall = [];

                for ($h = 0; $h < 24; $h++) {
                    $hourlyTemps[]     = rand(22, 34);
                    $hourlyHumidity[]  = rand(40, 95);
                    $hourlyPressure[]  = rand(990, 1015);
                    $hourlyWindSpeed[] = rand(1, 15);
                    $hourlyWindDir[]   = $windDirections[array_rand($windDirections)];
                    $hourlyRainfall[]  = rand(0, 5);
                }

                $directionCount = array_count_values($hourlyWindDir);
                arsort($directionCount);
                $dominantDirFull = array_key_first($directionCount); // sudah "Bertiup dari ..."

                LaporanHarian::create([
                    'aws_id' => $aws->id,
                    'date' => $tanggal,

                    'min_temperature' => min($hourlyTemps),
                    'max_temperature' => max($hourlyTemps),
                    'avg_temperature' => round(array_sum($hourlyTemps) / count($hourlyTemps), 2),

                    'min_humidity' => min($hourlyHumidity),
                    'max_humidity' => max($hourlyHumidity),
                    'avg_humidity' => round(array_sum($hourlyHumidity) / count($hourlyHumidity), 2),

                    'min_pressure' => min($hourlyPressure),
                    'max_pressure' => max($hourlyPressure),
                    'avg_pressure' => round(array_sum($hourlyPressure) / count($hourlyPressure), 2),

                    'total_rainfall' => array_sum($hourlyRainfall),
                    'rainfall_max'   => max($hourlyRainfall),
                    'rainy_days'     => array_sum($hourlyRainfall) > 0 ? 1 : 0,

                    'wind_speed_min' => min($hourlyWindSpeed),
                    'wind_speed_max' => max($hourlyWindSpeed),
                    'wind_speed_avg' => round(array_sum($hourlyWindSpeed) / count($hourlyWindSpeed), 2),

                    'dominant_wind_direction' => $dominantDirFull,
                ]);
            }
        }
    }
}
