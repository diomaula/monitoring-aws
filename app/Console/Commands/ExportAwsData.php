<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataAws;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ExportAwsData extends Command
{
    protected $signature = 'aws:export';
    protected $description = 'Export data AWS lama ke CSV dan hapus dari database';

    public function handle()
    {
        // Tentukan cutoff misalnya data lebih lama dari 1 bulan
        $cutoff = Carbon::now()->subMonth();

        // Ambil semua data lebih lama dari cutoff
        $data = DataAws::where('timestamp', '<', $cutoff)->get();

        if ($data->isEmpty()) {
            $this->info('Tidak ada data lama yang diexport.');
            return;
        }

        // Buat nama file CSV unik
        $filename = 'exports/aws_data_' . now()->format('Y_m_d_His') . '.csv';

        // Ambil nama kolom dari tabel
        $columns = array_keys($data->first()->toArray());

        // Buat array CSV
        $csvData = [];
        $csvData[] = $columns; // header
        foreach ($data as $row) {
            $csvData[] = array_values($row->toArray());
        }

        // Simpan ke storage/app/exports
        $handle = fopen(storage_path('app/' . $filename), 'w');
        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

        // Hapus data lama dari database
        DataAws::where('timestamp', '<', $cutoff)->delete();

        $this->info("Berhasil export " . count($data) . " record ke $filename dan hapus dari database.");
    }
}
