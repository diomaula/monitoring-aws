<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataAws;
use Carbon\Carbon;

class DataAwsSeeder extends Seeder
{
    public function run()
    {
        $startDate = Carbon::now()->subDays(7)->startOfDay(); // mulai 7 hari ke belakang
        $endDate   = Carbon::now();

        $awsList = [1, 2, 3];

        foreach ($awsList as $awsId) {
            $date = $startDate->copy();

            while ($date <= $endDate) {
                if ($date->hour % 3 === 0) { // hanya isi jam 0, 3, 6, 9, dst
                    // ✅ Cek dulu apakah data sudah ada
                    $exists = DataAws::where('aws_id', $awsId)
                        ->where('timestamp', $date)
                        ->exists();

                    if (!$exists) {
                        DataAws::create([
                            'aws_id'      => $awsId,
                            'timestamp'   => $date,
                            'rainfall'    => $this->generateRainfall($awsId),
                            'temperature' => $this->generateTemperature($awsId),
                            'humidity'    => $this->generateHumidity($awsId),
                        ]);
                    }
                }

                $date->addHour();
            }
        }
    }


    private function generateRainfall($awsId)
    {
        return rand(0, 20) + ($awsId * 2);
    }

    private function generateTemperature($awsId)
    {
        return rand(24, 32) + ($awsId - 1);
    }

    private function generateHumidity($awsId)
    {
        return rand(60, 90) - ($awsId * 2);
    }
}
