<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataAws;
use App\Models\LaporanHarian;
use Carbon\Carbon;
use DB;

class GenerateDailyReport extends Command
{
    protected $signature = 'report:daily';
    protected $description = 'Generate daily AWS report from hourly collected data';

    public function handle()
    {
        $date = Carbon::yesterday()->toDateString();

        $awsList = DataAws::select('aws_id')->distinct()->pluck('aws_id');

        foreach ($awsList as $awsId) {

            $data = DataAws::where('aws_id', $awsId)
                ->whereDate('timestamp', $date)
                ->get();

            if ($data->isEmpty()) {
                $this->info("Tidak ada data untuk AWS {$awsId} pada tanggal {$date}.");
                continue;
            }

            LaporanHarian::updateOrCreate(
                [
                    'aws_id' => $awsId,
                    'date' => $date
                ],
                [
                    'min_temperature' => $data->min('temperature'),
                    'max_temperature' => $data->max('temperature'),
                    'avg_temperature' => $data->avg('temperature'),

                    'min_humidity' => $data->min('humidity'),
                    'max_humidity' => $data->max('humidity'),
                    'avg_humidity' => $data->avg('humidity'),

                    'min_pressure' => $data->min('pressure'),
                    'max_pressure' => $data->max('pressure'),
                    'avg_pressure' => $data->avg('pressure'),

                    'total_rainfall' => $data->sum('rainfall'),

                    'avg_wind_speed' => $data->avg('wind_speed'),

                    'dominant_wind_direction' => $data->groupBy('wind_direction')
                        ->sortByDesc(fn ($group) => count($group))
                        ->keys()
                        ->first(),
                ]
            );

            $this->info("Laporan harian untuk AWS {$awsId} pada {$date} berhasil dibuat.");
        }

        $this->info("Proses generate laporan selesai.");
        return Command::SUCCESS;
    }
}