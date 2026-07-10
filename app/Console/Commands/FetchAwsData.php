<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\DataAws;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FetchAwsData extends Command
{
    protected $signature = 'aws:fetch-hourly';

    protected $description = 'Ambil data AWS dan deteksi anomali';

    public function handle()
    {
        $nowUtc = Carbon::now('UTC')->startOfHour();

        $awsStations = [
            '3000000007' => 2,
            '3000000046' => 3,
            '5000000031' => 1,
        ];

        foreach ($awsStations as $code => $awsId) {

            try {

                // Ambil data AWS
                $response = Http::timeout(15)
                    ->retry(3, 2000)
                    ->get("http://202.90.199.132/aws-new/data/station/latest/{$code}");

                if (!$response->successful()) {

                    Log::warning(
                        "AWS {$awsId} gagal HTTP {$response->status()}"
                    );

                    continue;
                }

                $json = $response->json();

                // Data lengkap untuk database
                $fullData = [
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
                ];

                // Data untuk Python
                $predictData = [
                    'aws_id'      => $awsId,
                    'timestamp'   => $fullData['timestamp'],
                    'temperature' => $fullData['temperature'] ?? 0,
                    'humidity'    => $fullData['humidity'] ?? 0,
                    'pressure'    => $fullData['pressure'] ?? 0,
                    'watertemp'   => $fullData['watertemp'] ?? 0,
                    'waterlevel'  => $fullData['waterlevel'] ?? 0,
                    'solrad'      => $fullData['solrad'] ?? 0,
                ];

                // Simpan sementara untuk Python
                $tempPath = storage_path('app/temp_predict.json');

                file_put_contents(
                    $tempPath,
                    json_encode($predictData)
                );

                // Jalankan Python
                $pythonPath = "python";

                $scriptPath = base_path(
                    'python_anomali/predict.py'
                );

                $command =
                    "\"{$pythonPath}\" ".
                    "\"{$scriptPath}\" ".
                    "< \"{$tempPath}\" 2>&1";

                $output = shell_exec($command);

                $result = json_decode($output, true);

                // Validasi hasil Python
                if (!$result || isset($result['error'])) {

                    Log::error(
                        "Predict gagal AWS {$awsId}: " . $output
                    );

                    continue;
                }

                $score = $result['score'] ?? null;

                $status = $result['status'] ?? 'NORMAL';

                // Simpan ke database
                DataAws::create(array_merge(
                    $fullData,
                    [
                        'anomaly_score' => $score,
                        'status'        => $status,
                    ]
                ));

                Log::info(
                    "AWS {$awsId} berhasil disimpan. Status: {$status}, Score: {$score}"
                );

            } catch (\Throwable $e) {

                Log::error(
                    "Error AWS {$awsId}: {$e->getMessage()}"
                );
            }
        }

        return Command::SUCCESS;
    }
}