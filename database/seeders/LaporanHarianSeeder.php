<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LaporanHarian;
use App\Models\Aws;
use Carbon\Carbon;

class LaporanHarianSeeder extends Seeder
{
    // public function run()
    // {
    //     DB::table('laporan_harian')->truncate();

    //     $awsList = Aws::all();
    //     if ($awsList->count() == 0) {
    //         $awsList = collect([
    //             Aws::create(['nama' => 'AWS Default', 'lokasi' => 'Test'])
    //         ]);
    //     }

    //     $windDirections = [
    //         'N'  => 'Bertiup dari Utara',
    //         'NE' => 'Bertiup dari Timur Laut',
    //         'E'  => 'Bertiup dari Timur',
    //         'SE' => 'Bertiup dari Tenggara',
    //         'S'  => 'Bertiup dari Selatan',
    //         'SW' => 'Bertiup dari Barat Daya',
    //         'W'  => 'Bertiup dari Barat',
    //         'NW' => 'Bertiup dari Barat Laut',
    //     ];

    //     foreach ($awsList as $aws) {
    //         for ($day = 0; $day < 60; $day++) {

    //             $tanggal = Carbon::now()->subDays($day)->toDateString();

    //             $hourlyTemps = [];
    //             $hourlyHumidity = [];
    //             $hourlyPressure = [];
    //             $hourlyWindSpeed = [];
    //             $hourlyWindDir = [];
    //             $hourlyRainfall = [];

    //             for ($h = 0; $h < 24; $h++) {

    //                 // --- Generate nilai iklim realistis ---
    //                 $hourlyTemps[]     = rand(22, 34);          // 22–34°C
    //                 $hourlyHumidity[]  = rand(50, 95);          // 50–95%
    //                 $hourlyPressure[]  = rand(1005, 1015);      // hPa
    //                 $hourlyWindSpeed[] = rand(5, 20) / 4;       // 1.25–5 m/s
    //                 $hourlyWindDir[]   = $windDirections[array_rand($windDirections)];

    //                 // --- Realistic Rainfall ---
    //                 // 15% kemungkinan hujan per jam, sisanya NILAI 0.0
    //                 $isRain = rand(1, 100) <= 15; // 15% chance

    //                 if ($isRain) {
    //                     // hujan bisa 0.1 - 4.0 mm
    //                     $hourlyRainfall[] = rand(1, 40) / 10;
    //                 } else {
    //                     // NULL hujan
    //                     $hourlyRainfall[] = 0.0;
    //                 }
    //             }

    //             // Hitung dominan arah angin
    //             $directionCount = array_count_values($hourlyWindDir);
    //             arsort($directionCount);
    //             $dominantDirFull = array_key_first($directionCount);

    //             // Total hujan harian
    //             $total_rainfall = array_sum($hourlyRainfall);

    //             LaporanHarian::create([
    //                 'aws_id' => $aws->id,
    //                 'date' => $tanggal,

    //                 'min_temperature' => min($hourlyTemps),
    //                 'max_temperature' => max($hourlyTemps),
    //                 'avg_temperature' => round(array_sum($hourlyTemps) / count($hourlyTemps), 2),

    //                 'min_humidity' => min($hourlyHumidity),
    //                 'max_humidity' => max($hourlyHumidity),
    //                 'avg_humidity' => round(array_sum($hourlyHumidity) / count($hourlyHumidity), 2),

    //                 'min_pressure' => min($hourlyPressure),
    //                 'max_pressure' => max($hourlyPressure),
    //                 'avg_pressure' => round(array_sum($hourlyPressure) / count($hourlyPressure), 2),

    //                 'total_rainfall' => $total_rainfall,
    //                 'rainfall_max'   => max($hourlyRainfall),
    //                 'rainy_days'     => $total_rainfall > 0 ? 1 : 0, // 0 jika tidak ada hujan seharian

    //                 'wind_speed_min' => min($hourlyWindSpeed),
    //                 'wind_speed_max' => max($hourlyWindSpeed),
    //                 'wind_speed_avg' => round(array_sum($hourlyWindSpeed) / count($hourlyWindSpeed), 2),

    //                 'dominant_wind_direction' => $dominantDirFull,
    //             ]);
    //         }
    //     }
    // }
}
