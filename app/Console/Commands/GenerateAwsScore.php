<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataAws;
use Symfony\Component\Process\Process;

class GenerateAwsScore extends Command
{
    protected $signature = 'aws:generate-score';
    protected $description = 'Generate anomaly score (batch cepat & aman)';

    public function handle()
    {
        $this->info("🚀 Generate anomaly score (BATCH MODE)");

        // =============================
        // AMBIL DATA BELUM ADA SCORE
        // =============================
        $dataList = DataAws::where(function ($q) {
            $q->whereNull('anomaly_score')
            ->orWhereNull('status');
        })
        ->get();

        if ($dataList->isEmpty()) {
            $this->info("✅ Semua data sudah memiliki score");
            return Command::SUCCESS;
        }

        $this->info("📊 Total data diproses: " . $dataList->count());

        // =============================
        // PREPARE PAYLOAD
        // =============================
        $payload = [];

        foreach ($dataList as $data) {
            $payload[] = [
                "id" => $data->id,
                "aws_id" => (int) $data->aws_id,
                "timestamp" => (string) $data->timestamp,
                "temperature" => (float) $data->temperature,
                "humidity" => (float) $data->humidity,
                "pressure" => (float) $data->pressure,
                "watertemp" => (float) $data->watertemp,
                "waterlevel" => (float) $data->waterlevel,
                "solrad" => (float) $data->solrad,
            ];
        }

        // =============================
        // JALANKAN PYTHON
        // =============================
        $process = new Process([
            'python',
            base_path('python_anomali/predict_batch.py')
        ]);

        $process->setInput(json_encode($payload));
        $process->setTimeout(300);
        $process->run();

        // =============================
        // HANDLE ERROR PROCESS
        // =============================
        if (!$process->isSuccessful()) {
            $this->error("❌ Python gagal:");
            $this->error($process->getErrorOutput());
            return Command::FAILURE;
        }

        $output = $process->getOutput();

        // =============================
        // VALIDASI OUTPUT
        // =============================
        if (!$output) {
            $this->error("❌ Output kosong dari Python");
            return Command::FAILURE;
        }

        $results = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("❌ JSON ERROR: " . json_last_error_msg());
            $this->error("RAW:");
            $this->error($output);
            return Command::FAILURE;
        }

        if (isset($results['error'])) {
            $this->error("❌ Python ERROR:");
            $this->error($results['error']);
            return Command::FAILURE;
        }

        // =============================
        // UPDATE DATABASE
        // =============================
        $updated = 0;

        foreach ($results as $res) {

            if (!is_array($res) || !isset($res['id'])) {
                continue;
            }

            DataAws::where('id', $res['id'])
            ->update([
                'anomaly_score' => $res['score'],
                'status'        => $res['status']
            ]);

            $updated++;
        }

        // =============================
        // RESULT
        // =============================
        $this->info("✅ Selesai!");
        $this->info("✔ Data berhasil diupdate: $updated");

        return Command::SUCCESS;
    }
}