<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use Maatwebsite\Excel\Facades\Excel;
// use Carbon\Carbon;
// use App\Models\DataAws;

// class ImportAws extends Command
// {
//     protected $signature = 'aws:import';
//     protected $description = 'Import data AWS Jan-Feb dari Excel ke database';

//     public function handle()
//     {
//         $file = base_path('python_anomali/data/data_aws_jan-feb.xlsx');

//         $this->info("📥 Import file: $file");

//         $rows = Excel::toArray([], $file)[0];

//         // hapus header
//         unset($rows[0]);

//         $inserted = 0;

//         foreach ($rows as $row) {
//             try {

//                 DataAws::create([
//                     'aws_id' => (int) $row[0],

//                     'timestamp' => Carbon::parse($row[7]),

//                     'temperature' => $this->toFloat($row[1]),
//                     'humidity'    => $this->toFloat($row[2]),
//                     'pressure'    => $this->toFloat($row[3]),
//                     'solrad'      => $this->toFloat($row[4]),
//                     'waterlevel'  => $this->toFloat($row[5]),
//                     'watertemp'   => $this->toFloat($row[6]),

//                     // kolom lain tetap disimpan NULL (tidak ada di Excel)
//                     'rainfall'       => null,
//                     'wind_speed'     => null,
//                     'wind_direction' => null,
//                     'pancitemp'      => null,
//                     'pancilevel'     => null,

//                     // nanti diisi setelah predict
//                     'status'         => null,
//                     'anomaly_score'  => null,
//                 ]);

//                 $inserted++;

//             } catch (\Exception $e) {
//                 $this->error("❌ Error row: " . json_encode($row));
//                 $this->error($e->getMessage());
//             }
//         }

//         $this->info("✅ Import selesai: $inserted data");

//         return Command::SUCCESS;
//     }

//     // =============================
//     // CONVERT ANGKA KOMA → TITIK
//     // =============================
//     private function toFloat($value)
//     {
//         if ($value === null || $value === '') return null;

//         // ubah koma jadi titik
//         $value = str_replace(',', '.', $value);

//         return is_numeric($value) ? (float) $value : null;
//     }


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\DataAws;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportAws extends Command
{
    protected $signature = 'aws:import';
    protected $description = 'Import data AWS Maret-Juni dari Excel ke database';

    public function handle()
    {
        $file = base_path('python_anomali/data/data_aws_maret-juni.xlsx');

        $this->info("📥 Import file: $file");

        $rows = Excel::toArray([], $file)[0];

        // Hapus header
        unset($rows[0]);

        $inserted = 0;
        $skipped = 0;

        foreach ($rows as $row) {

            try {
                if (is_numeric($row[12])) {
                    $timestamp = Carbon::instance(
                        Date::excelToDateTimeObject($row[12])
                    );
                } else {
                    $timestamp = Carbon::parse($row[12]);
                }

                // Cek apakah data sudah ada
                $exists = DataAws::where('aws_id', (int)$row[0])
                    ->where('timestamp', $timestamp)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                DataAws::create([

                    'aws_id'         => (int) $row[0],
                    'timestamp'      => $timestamp,

                    'wind_speed'     => $this->toFloat($row[1]),
                    'wind_direction' => $this->toFloat($row[2]),

                    'temperature'    => $this->toFloat($row[3]),
                    'humidity'       => $this->toFloat($row[4]),
                    'pressure'       => $this->toFloat($row[5]),
                    'rainfall'       => $this->toFloat($row[6]),
                    'solrad'         => $this->toFloat($row[7]),
                    'watertemp'      => $this->toFloat($row[8]),
                    'waterlevel'     => $this->toFloat($row[9]),
                    'pancilevel'     => $this->toFloat($row[10]),
                    'pancitemp'      => $this->toFloat($row[11]),

                    'status'         => null,
                    'anomaly_score'  => null,
                ]);

                $inserted++;

            } catch (\Exception $e) {

                $this->error("❌ Error row: " . json_encode($row));
                $this->error($e->getMessage());

            }
        }

        $this->info("=================================");
        $this->info("✅ Import selesai");
        $this->info("✔ Data baru : $inserted");
        $this->info("⏭ Data dilewati : $skipped");

        return Command::SUCCESS;
    }

    private function toFloat($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float)$value : null;
    }

}

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use Maatwebsite\Excel\Facades\Excel;
// use Carbon\Carbon;
// use App\Models\DataAws;

// class ImportAws extends Command
// {
//     protected $signature = 'aws:import';
//     protected $description = 'Import data AWS dari Excel ke database';

//     public function handle()
//     {
//         $file = base_path('python_anomali/data/data_aws_jan-jun_clean.xlsx');

//         $this->info("📥 Import file: $file");

//         $rows = Excel::toArray([], $file)[0];

//         // hapus header
//         unset($rows[0]);

//         $inserted = 0;

//         foreach ($rows as $row) {
//             try {

//                 DataAws::create([
//                     'aws_id' => (int) $row[0],

//                     'timestamp' => Carbon::parse($row[12]),

//                     'temperature'    => $this->toFloat($row[3]),
//                     'humidity'       => $this->toFloat($row[4]),
//                     'pressure'       => $this->toFloat($row[5]),
//                     'rainfall'       => $this->toFloat($row[6]),

//                     'wind_speed'     => $this->toFloat($row[1]),
//                     'wind_direction' => $this->toFloat($row[2]),

//                     'solrad'         => $this->toFloat($row[7]),
//                     'watertemp'      => $this->toFloat($row[8]),
//                     'waterlevel'     => $this->toFloat($row[9]),

//                     'pancilevel'     => $this->toFloat($row[10]),
//                     'pancitemp'      => $this->toFloat($row[11]),

//                     // akan diisi saat proses prediksi
//                     'status'         => null,
//                     'anomaly_score'  => null,
//                 ]);

//                 $inserted++;

//             } catch (\Exception $e) {
//                 $this->error("❌ Error row: " . json_encode($row));
//             }
//         }

//         $this->info("✅ Import selesai: $inserted data");

//         return Command::SUCCESS;
//     }

//     // =============================
//     // CONVERT ANGKA KOMA → TITIK
//     // =============================
//     private function toFloat($value)
//     {
//         if ($value === null || $value === '') return null;

//         // ubah koma jadi titik
//         $value = str_replace(',', '.', $value);

//         return is_numeric($value) ? (float) $value : null;
//     }
