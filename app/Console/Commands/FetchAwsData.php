<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\DataAws;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\DB;

class FetchAwsData extends Command
{
    protected $signature = 'aws:fetch-hourly';
    protected $description = 'Ambil data mentah AWS setiap jam (1 data representatif)';

    public function handle()
    {
        $nowUtc = Carbon::now('UTC')->startOfHour();

        // Log ringan (1 baris per jam)
        Log::info("Cron aws:fetch-hourly dijalankan pada UTC {$nowUtc->toDateTimeString()}");

        $awsStations = [
            '3000000007' => 2, // AWS Maritim Ketapang
            '3000000046' => 3, // AWS Maritim Gilimanuk
            '5000000031' => 1, // AWS Digi Banyuwangi
        ];

        foreach ($awsStations as $code => $awsId) {
            try {
                $response = Http::retry(3, 2000)
                ->timeout(15)
                ->get("http://202.90.199.132/aws-new/data/station/latest/{$code}");


                if (!$response->successful()) {
                    Log::warning("AWS {$awsId} gagal diambil, HTTP {$response->status()}");
                    continue;
                }

                $json = $response->json();

                DataAws::create([
                    'aws_id'         => $awsId,
                    'timestamp'      => $json['waktu'] ?? $nowUtc,
                    'temperature'    => $json['temp'] ?? null,
                    'humidity'       => $json['rh'] ?? null,
                    'pressure'       => $json['pressure'] ?? null,
                    'rainfall'       => $json['rain'] ?? null,
                    'wind_speed'     => $json['windspeed'] ?? null,
                    'wind_direction' => $json['winddir'] ?? null,
                    'pancitemp'      => $json['pancitemp'] ?? null,
                    'pancilevel'     => $json['pancilevel'] ?? null,
                    'solrad'         => $json['solrad'] ?? null,
                    'watertemp'      => $json['watertemp'] ?? null,
                    'waterlevel'     => $json['waterlevel'] ?? null,
                ]);
            } catch (\Throwable $e) {
                Log::error("Exception AWS {$awsId}: {$e->getMessage()}");
            }
        }

        return Command::SUCCESS;
    }
}