<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\AwsStatusLog;

class AwsStatusLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stations = [
            ['id' => '5000000031', 'name' => 'AWS Digi Banyuwangi'],
            ['id' => '3000000007', 'name' => 'AWS Maritim Ketapang'],
            ['id' => '3000000046', 'name' => 'AWS Maritim Gilimanuk'],
        ];

        $endDate = Carbon::now('Asia/Jakarta');
        $startDate = $endDate->copy()->subDays(7);

        foreach ($stations as $station) {

            for ($i = 0; $i <= 7; $i++) {
                // Tentukan tanggal
                $day = $startDate->copy()->addDays($i);

                // ==== Pagi ====
                $pagiMati = $day->copy()->setTime(8, 0, 0); // jam 08:00:00
                $pagiHidup = $pagiMati->copy()->addMinutes(2); // durasi 2 menit

                AwsStatusLog::create([
                    'station_id' => $station['id'],
                    'name' => $station['name'],
                    'status' => 'mati',
                    'waktu' => $pagiMati,
                    'created_at' => $pagiMati,
                    'updated_at' => $pagiMati,
                ]);

                AwsStatusLog::create([
                    'station_id' => $station['id'],
                    'name' => $station['name'],
                    'status' => 'hidup',
                    'waktu' => $pagiHidup,
                    'created_at' => $pagiHidup,
                    'updated_at' => $pagiHidup,
                ]);

                // ==== Sore ====
                $soreMati = $day->copy()->setTime(16, 0, 0); // jam 16:00:00
                $soreHidup = $soreMati->copy()->addMinutes(1)->addSeconds(7); // durasi 1 menit 7 detik

                AwsStatusLog::create([
                    'station_id' => $station['id'],
                    'name' => $station['name'],
                    'status' => 'mati',
                    'waktu' => $soreMati,
                    'created_at' => $soreMati,
                    'updated_at' => $soreMati,
                ]);

                AwsStatusLog::create([
                    'station_id' => $station['id'],
                    'name' => $station['name'],
                    'status' => 'hidup',
                    'waktu' => $soreHidup,
                    'created_at' => $soreHidup,
                    'updated_at' => $soreHidup,
                ]);
            }
        }

        $this->command->info('Seeder AWS Status Logs 7 hari selesai!');
    }
}
