<?php

// app/Console/Commands/ExportAwsData.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataAws;
use Illuminate\Support\Facades\Storage;

class ExportAwsData extends Command
{
    protected $signature = 'aws:export';
    protected $description = 'Export AWS data older than 1 month to CSV and delete them';

    public function handle()
    {
        $cutoff = now()->subMonth(); // ambil data lebih dari 1 bulan
        $data = DataAws::where('timestamp', '<', $cutoff)->get();

        if ($data->isEmpty()) {
            $this->info("Tidak ada data lama untuk di-export.");
            return;
        }

        // buat nama file, contoh: aws-data-2025-08.csv
        $filename = "aws-data-" . now()->subMonth()->format('Y-m') . ".csv";

        // header CSV
        $csvData = [];
        $csvData[] = ['id', 'aws_id', 'rainfall', 'temperature', 'humidity', 'timestamp'];

        foreach ($data as $row) {
            $csvData[] = [
                $row->id,
                $row->aws_id,
                $row->rainfall,
                $row->temperature,
                $row->humidity,
                $row->timestamp,
            ];
        }

        // simpan ke storage/app/exports
        $handle = fopen(storage_path("app/exports/{$filename}"), 'w');
        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

        // hapus data lama
        DataAws::where('timestamp', '<', $cutoff)->delete();

        $this->info("Data lama berhasil di-export ke {$filename} dan dihapus dari DB.");
    }
}
