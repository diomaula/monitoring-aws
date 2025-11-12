<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\DataAws;
use Carbon\Carbon;

class FetchAwsData extends Command
{
    protected $signature = 'aws:fetch';
    protected $description = 'Ambil data dari API AWS dan simpan ke database setiap 1 jam UTC';

    public function handle()
    {
        $awsStations = [
            '3000000007' => 2, // AWS Maritim Ketapang
            '3000000046' => 3, // AWS Maritim Gilimanuk
            '5000000031' => 1, // AWS Digi Banyuwangi
        ];

        $nowUtc = Carbon::now('UTC');
        $hour = (int) $nowUtc->format('H');

        // hanya jalan kalau jam UTC kelipatan 3 (00, 03, 06, dst.)
        if ($hour % 1 !== 0) {
            $this->info("Menjalankan fetch AWS pada " . now('UTC'));
            return 0;
        }

        foreach ($awsStations as $code => $awsId) {
            $url = "http://202.90.199.132/aws-new/data/station/latest/{$code}";
            $response = Http::get($url);

            if ($response->successful()) {
                $json = $response->json();

                DataAws::create([
                    'aws_id'        => $awsId,
                    'timestamp'     => $json['waktu'] ?? $nowUtc, // fallback ke sekarang
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

                $this->info("Data AWS {$awsId} berhasil disimpan.");
            } else {
                $this->error("Gagal ambil data AWS {$awsId}");
            }
        }

        return 0;
    }
}
