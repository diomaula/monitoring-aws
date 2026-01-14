<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\AwsStatusLog;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CheckAwsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:aws-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek Status AWS: Normal atau Anomali (Menggunakan AI + Cek Null)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Mapping ID: 'ID_API_BMKG' => ID_DATABASE_LOKAL
        $stations = [
            '5000000031' => 1, // AWS Digi Banyuwangi
            '3000000007' => 2, // AWS Maritim Ketapang
            '3000000046' => 3, // AWS Maritim Gilimanuk
        ];

        foreach ($stations as $apiId => $dbId) {
            // 1. Ambil Data Nama dari Database
            $awsLocal = \App\Models\Aws::find($dbId);
            
            if (!$awsLocal) {
                $this->error("ID Database $dbId tidak ditemukan. Lewati.");
                continue;
            }

            $name = $awsLocal->name; // Simpan nama ke variabel
            $this->info("Memproses $name...");

            // 2. Ambil Data API
            try {
                $response = Http::timeout(10)->get("http://202.90.199.132/aws-new/data/station/latest/$apiId");
                
                if (!$response->successful()) {
                    $this->error("Gagal API untuk $name");
                    continue; 
                }

                $json = $response->json();
            } catch (\Exception $e) {
                $this->error("Koneksi Error: " . $e->getMessage());
                continue;
            }

            // Siapkan Parameter
            $temp  = $json['temp'] ?? 0;
            $rh    = $json['rh'] ?? 0;
            $press = $json['pressure'] ?? 1010;
            $wind  = $json['windspeed'] ?? 0;
            $rain  = $json['rain'] ?? 0;

            // 3. Logika Penentuan Status
            $isMati = ($temp == 0 && $rh == 0 && $wind == 0 && $rain == 0);
            $status = 'Normal';
            $keterangan = 'Kondisi operasional wajar.';
            
            // Inisialisasi variabel score agar tidak error saat save
            $aiScore = null; 

            if ($isMati) {
                $status = 'Anomali';
                $aiScore = -1.0; // Berikan skor fix jika mati total (opsional)
                $keterangan = 'Indikasi Alat Mati/Offline (Semua sensor bernilai 0).';
            } 
            else {
                // Panggil Python AI
                try {
                    $process = new Process([
                        'python', 
                        base_path('python_scripts/predict.py'),
                        $temp, $rh, $press, $wind, $rain
                    ]);
                    
                    $process->run();

                    if ($process->isSuccessful()) {
                        $output = json_decode($process->getOutput(), true);
                        
                        // Ambil score dari output python (jika ada)
                        $predictionScore = $output['score'] ?? null;

                        if (isset($output['prediction']) && $output['prediction'] === 'Anomali') {
                            $status = 'Anomali';
                            $aiScore = $predictionScore; // Simpan score ke variabel
                            $keterangan = "Terdeteksi pola data menyimpang.";
                        } else {
                            // Jika Normal, kita tetap bisa simpan score positifnya jika mau
                            // atau biarkan null. Di sini saya simpan jika ada.
                            $aiScore = $predictionScore; 
                        }
                    }
                } catch (\Exception $e) {
                    $this->warn("Gagal menjalankan Python: " . $e->getMessage());
                }
            }

            // 4. Simpan ke Database
            // Cek log terakhir untuk menghindari spam log jika status sama
            $lastLog = AwsStatusLog::where('aws_id', $dbId)->orderBy('id', 'desc')->first();

            $harusSimpan = false;
            if (!$lastLog) $harusSimpan = true;
            elseif ($lastLog->status !== $status) $harusSimpan = true;
            elseif ($status === 'Anomali') $harusSimpan = true; // Selalu catat jika Anomali

            if ($harusSimpan) {
                AwsStatusLog::create([
                    'aws_id'      => $dbId,
                    'name'        => $name,        // <--- INI PENTING (Dulu belum ada)
                    'status'      => $status,
                    'ai_score'    => $aiScore,     // <--- INI PENTING (Dulu belum ada)
                    'description' => $keterangan,
                    'waktu'       => Carbon::now('Asia/Jakarta'),
                ]);

                $color = $status === 'Normal' ? 'info' : 'error';
                $this->$color("[$name] -> Tersimpan: $status (Score: $aiScore)");
            } else {
                $this->line("[$name] -> Stabil ($status)");
            }
        }

        return 0;
    }
}