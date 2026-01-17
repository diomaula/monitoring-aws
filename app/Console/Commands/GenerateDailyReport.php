<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataAws;
use App\Models\LaporanHarian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateDailyReport extends Command
{
    protected $signature = 'report:daily';
    protected $description = 'Generate laporan harian AWS dari data per jam';

    public function handle()
    {
        $date = Carbon::yesterday('UTC')->toDateString();

        Log::info("GenerateDailyReport mulai untuk tanggal {$date}");

        $awsList = DataAws::select('aws_id')->distinct()->pluck('aws_id');

        foreach ($awsList as $awsId) {

            $hourlyData = collect();

            for ($hour = 0; $hour < 24; $hour++) {

                $start = Carbon::parse($date, 'UTC')
                    ->setHour($hour)->minute(55)->second(0);

                $end = Carbon::parse($date, 'UTC')
                    ->setHour($hour)->minute(59)->second(59);

                $raw = DataAws::where('aws_id', $awsId)
                    ->whereBetween('timestamp', [$start, $end])
                    ->get();

                if ($raw->count() < 3) continue;

                $hourlyData->push([
                    'temperature' => $raw->avg('temperature'),
                    'humidity'    => $raw->avg('humidity'),
                    'pressure'    => $raw->avg('pressure'),
                    'rainfall'    => $raw->sum('rainfall'),
                    'wind_speed'  => $raw->avg('wind_speed'),
                    'wind_direction' => $raw->groupBy('wind_direction')
                        ->sortByDesc(fn ($g) => $g->count())
                        ->keys()
                        ->first(),
                ]);
            }

            if ($hourlyData->isEmpty()) continue;

            LaporanHarian::updateOrCreate(
                [
                    'aws_id' => $awsId,
                    'date'   => $date,
                ],
                [
                    'min_temperature' => $hourlyData->min('temperature'),
                    'max_temperature' => $hourlyData->max('temperature'),
                    'avg_temperature' => $hourlyData->avg('temperature'),

                    'min_humidity' => $hourlyData->min('humidity'),
                    'max_humidity' => $hourlyData->max('humidity'),
                    'avg_humidity' => $hourlyData->avg('humidity'),

                    'min_pressure' => $hourlyData->min('pressure'),
                    'max_pressure' => $hourlyData->max('pressure'),
                    'avg_pressure' => $hourlyData->avg('pressure'),

                    'total_rainfall' => $hourlyData->sum('rainfall'),
                    'avg_wind_speed' => $hourlyData->avg('wind_speed'),

                    'dominant_wind_direction' => $hourlyData
                        ->groupBy('wind_direction')
                        ->sortByDesc(fn ($g) => $g->count())
                        ->keys()
                        ->first(),
                ]
            );
        }

        return Command::SUCCESS;
    }
}