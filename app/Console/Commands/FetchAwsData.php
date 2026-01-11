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
    protected $description = 'Ambil data mentah AWS setiap jam dan jalankan prediksi AI';

    public function handle()
    {
        $nowUtc = Carbon::now('UTC')->startOfHour();

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

                // 1. Simpan Data AWS
                $dataAws = DataAws::create([
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

                // ---------------------------------------------------------
                // 2. INTEGRASI AI (Nowcasting)
                // ---------------------------------------------------------
                
                // Hanya jalankan prediksi jika ada data suhu
                if (isset($json['temp'])) {
                    $this->runPrediction($awsId);
                }

            } catch (\Throwable $e) {
                Log::error("Exception AWS {$awsId}: {$e->getMessage()}");
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Fungsi khusus untuk memanggil script Python AI
     */
    private function runPrediction($awsId)
    {
        try {
            // Ambil 60 data suhu terakhir KHUSUS untuk station ini
            $lastData = DataAws::where('aws_id', $awsId)
                ->orderBy('timestamp', 'desc') // Ambil dari yang paling baru
                ->take(60)
                ->pluck('temperature')
                ->toArray();

            // AI butuh minimal beberapa data, kalau kosong skip aja
            if (count($lastData) > 0) {
                // Balik urutan jadi (Lama -> Baru) untuk Time Series
                $lastData = array_reverse($lastData);
                $inputString = implode(',', $lastData);

                // Setup Path Script Python
                $scriptPath = base_path('python_scripts/predict.py');
                
                // Jalankan Python Process
                // Menggunakan array ['python', path, argumen] lebih aman
                $process = new Process(['python', $scriptPath, $inputString]);
                $process->setTimeout(60); // Timeout 1 menit mencegah hang
                $process->run();

                // Cek apakah berhasil
                if (!$process->isSuccessful()) {
                    Log::error("AI Error (AWS {$awsId}): " . $process->getErrorOutput());
                } else {
                    $output = trim($process->getOutput());

                    // Validasi output apakah angka valid
                    if (is_numeric($output)) {
                        $prediksiSuhu = (float) $output;

                        // Simpan ke tabel predictions
                        // Note: Pastikan tabel predictions sudah dibuat lewat migration
                        DB::table('predictions')->insert([
                            'waktu_prediksi' => Carbon::now()->addHour(), // Prediksi 1 jam ke depan
                            'nilai_prediksi' => $prediksiSuhu,
                            'tipe_sensor'    => 'temperature',
                            // Opsional: Jika tabel predictions Anda punya kolom aws_id, uncomment baris bawah:
                            // 'aws_id'      => $awsId, 
                            'created_at'     => Carbon::now(),
                            'updated_at'     => Carbon::now(),
                        ]);

                        Log::info("AI Success AWS {$awsId}: Prediksi Suhu {$prediksiSuhu}°C");
                        $this->info("AWS {$awsId} - Prediksi: {$prediksiSuhu}°C");
                    } else {
                        Log::warning("AI Output Invalid (AWS {$awsId}): {$output}");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("AI Logic Failed: " . $e->getMessage());
        }
    }
}