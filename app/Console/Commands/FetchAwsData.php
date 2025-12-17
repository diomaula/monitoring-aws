<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\DataAws;
use Carbon\Carbon;

class FetchAwsData extends Command
{
    protected $signature = 'aws:fetch-raw';
    protected $description = 'Ambil data mentah AWS (H-5 s.d H-1)';

    public function handle()
    {
        $awsStations = [
            '3000000007' => 2,
            '3000000046' => 3,
            '5000000031' => 1,
        ];

        $nowUtc = Carbon::now('UTC');

        // simpan menit 55–59
        if ($nowUtc->minute < 55) {
            return Command::SUCCESS;
        }

        foreach ($awsStations as $code => $awsId) {

            $response = Http::timeout(10)
                ->get("http://202.90.199.132/aws-new/data/station/latest/{$code}");

            if (!$response->successful()) {
                $this->error("Gagal ambil data AWS {$awsId}");
                continue;
            }

            $json = $response->json();

            DataAws::create([
                'aws_id'        => $awsId,
                'timestamp'     => $json['waktu'] ?? $nowUtc,
                'temperature'   => $json['temp'] ?? null,
                'humidity'      => $json['rh'] ?? null,
                'pressure'      => $json['pressure'] ?? null,
                'rainfall'      => $json['rain'] ?? null,
                'wind_speed'    => $json['windspeed'] ?? null,
                'wind_direction'=> $json['winddir'] ?? null,
                'pancitemp'     => $json['pancitemp'] ?? null,
                'pancilevel'    => $json['pancilevel'] ?? null,
                'solrad'        => $json['solrad'] ?? null,
                'watertemp'     => $json['watertemp'] ?? null,
                'waterlevel'    => $json['waterlevel'] ?? null,
            ]);

            $this->info("RAW AWS {$awsId} tersimpan ({$nowUtc->format('H:i')})");
        }

        return Command::SUCCESS;
    }
}