<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataAws;
use Symfony\Component\Process\Process;

class GenerateAwsScore extends Command
{
    protected $signature = 'aws:generate-score';
    protected $description = 'Generate anomaly score dari model Python';

    public function handle()
    {
        $this->info("🚀 Generate anomaly score dimulai...");

        // ambil data yang belum ada score
        $dataList = DataAws::whereNull('anomaly_score')->get();

        $this->info("Total data: " . $dataList->count());

        $success = 0;
        $failed = 0;

        foreach ($dataList as $data) {
            try {

                // =============================
                // PAYLOAD KE PYTHON
                // =============================
                $payload = json_encode([
                    "aws_id" => (int) $data->aws_id,
                    "timestamp" => (string) $data->timestamp,
                    "temperature" => (float) $data->temperature,
                    "humidity" => (float) $data->humidity,
                    "pressure" => (float) $data->pressure,
                    "watertemp" => (float) $data->watertemp,
                    "waterlevel" => (float) $data->waterlevel,
                    "solrad" => (float) $data->solrad,
                ]);

                // =============================
                // JALANKAN PYTHON
                // =============================
                $process = new Process([
                    'python',
                    base_path('python_anomali/predict.py')
                ]);

                $process->setInput($payload);
                $process->run();

                if (!$process->isSuccessful()) {
                    throw new \Exception($process->getErrorOutput());
                }

                $output = json_decode($process->getOutput(), true);

                // =============================
                // SIMPAN SCORE
                // =============================
                $data->update([
                    'anomaly_score' => $output['score'],
                ]);

                $success++;

            } catch (\Throwable $e) {
                $failed++;
                $this->error("❌ Error ID {$data->id}");
            }
        }

        $this->info("✅ Selesai!");
        $this->info("✔ Success: $success");
        $this->info("❌ Failed: $failed");

        return Command::SUCCESS;
    }
}