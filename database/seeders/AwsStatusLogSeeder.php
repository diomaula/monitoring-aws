<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\AwsStatusLog;
use Illuminate\Support\Facades\DB;

class AwsStatusLogSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            ['code' => '5000000031', 'name' => 'AWS Digi Banyuwangi'],
            ['code' => '3000000007', 'name' => 'AWS Maritim Ketapang'],
            ['code' => '3000000046', 'name' => 'AWS Maritim Gilimanuk'],
        ];

        $endDate = Carbon::now('Asia/Jakarta');
        $startDate = $endDate->copy()->subDays(7);

        foreach ($stations as $station) {

            $aws = DB::table('aws')->where('code', $station['code'])->first();

            if (!$aws) {
                continue;
            }

            for ($i = 0; $i <= 7; $i++) {

                $day = $startDate->copy()->addDays($i);

                $pagiMati = $day->copy()->setTime(8, 0, 0);
                $pagiHidup = $pagiMati->copy()->addMinutes(2);

                AwsStatusLog::create([
                    'aws_id' => $aws->id,
                    'status' => 'mati',
                    'waktu' => $pagiMati,
                    'created_at' => $pagiMati,
                    'updated_at' => $pagiMati,
                ]);

                AwsStatusLog::create([
                    'aws_id' => $aws->id,
                    'status' => 'hidup',
                    'waktu' => $pagiHidup,
                    'created_at' => $pagiHidup,
                    'updated_at' => $pagiHidup,
                ]);

                $soreMati = $day->copy()->setTime(16, 0, 0);
                $soreHidup = $soreMati->copy()->addMinutes(1)->addSeconds(7);

                AwsStatusLog::create([
                    'aws_id' => $aws->id,
                    'status' => 'mati',
                    'waktu' => $soreMati,
                    'created_at' => $soreMati,
                    'updated_at' => $soreMati,
                ]);

                AwsStatusLog::create([
                    'aws_id' => $aws->id,
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