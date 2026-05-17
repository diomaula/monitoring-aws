<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\DataAws;

class ImportAwsJanFeb extends Command
{
    protected $signature = 'aws:import-janfeb';
    protected $description = 'Import data AWS Jan-Feb dari Excel ke database';

    public function handle()
    {
        $file = base_path('python_anomali/data/data_aws_jan-feb_clean.xlsx');

        $this->info("📥 Import file: $file");

        $rows = Excel::toArray([], $file)[0];

        // hapus header
        unset($rows[0]);

        $inserted = 0;

        foreach ($rows as $row) {
            try {

                DataAws::create([
                    'aws_id' => (int) $row[0],

                    'timestamp' => Carbon::parse($row[7]),

                    'temperature' => $this->toFloat($row[1]),
                    'humidity'    => $this->toFloat($row[2]),
                    'pressure'    => $this->toFloat($row[3]),
                    'watertemp'   => $this->toFloat($row[4]),
                    'waterlevel'  => $this->toFloat($row[5]),
                    'solrad'      => $this->toFloat($row[6]),

                    // kolom lain tetap disimpan NULL (tidak ada di Excel)
                    'rainfall'       => null,
                    'wind_speed'     => null,
                    'wind_direction' => null,
                    'pancitemp'      => null,
                    'pancilevel'     => null,

                    // nanti diisi setelah predict
                    'status'         => null,
                    'anomaly_score'  => null,
                ]);

                $inserted++;

            } catch (\Exception $e) {
                $this->error("❌ Error row: " . json_encode($row));
            }
        }

        $this->info("✅ Import selesai: $inserted data");

        return Command::SUCCESS;
    }

    // =============================
    // CONVERT ANGKA KOMA → TITIK
    // =============================
    private function toFloat($value)
    {
        if ($value === null || $value === '') return null;

        // ubah koma jadi titik
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : null;
    }
}