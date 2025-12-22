<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataAws; // Pastikan Model ini benar
use Illuminate\Support\Facades\File;

class ExportDataForAI extends Command
{
    /**
     * Nama command untuk dijalankan di terminal.
     */
    protected $signature = 'app:export-data-for-ai';

    /**
     * Deskripsi command.
     */
    protected $description = 'Export data AWS dari database ke CSV untuk training AI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses export data...');

        $filePath = storage_path('app/dataset_cuaca.csv');
        $file = fopen($filePath, 'w');

        $columns = [
            'timestamp',      
            'temperature',    
            'humidity',       
            'pressure',       
            'rainfall',       
            'wind_speed',     
            'wind_direction', 
            'solrad'          
        ];
        
        fputcsv($file, $columns);

        $query = DataAws::query()
                    ->select($columns) 
                    ->orderBy('timestamp', 'asc'); 

        $count = 0;
        
        foreach ($query->cursor() as $row) {
            fputcsv($file, [
                $row->timestamp,
                $row->temperature,
                $row->humidity,
                $row->pressure,
                $row->rainfall,
                $row->wind_speed,
                $row->wind_direction,
                $row->solrad
            ]);
            $count++;
        }

        fclose($file);

        $this->info("Berhasil! $count baris data telah diexport.");
        $this->info("Lokasi file: $filePath");
    }
}