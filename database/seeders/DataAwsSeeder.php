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
                DataAws::create([
                    'aws_id'      => $awsId,
                    'timestamp'   => $date,
                    'rainfall'    => $this->generateRainfall($awsId),
                    'temperature' => $this->generateTemperature($awsId),
                    'humidity'    => $this->generateHumidity($awsId),
                ]);

                $date->addHours(3); // interval 3 jam
            }
        }
    }

    private function generateRainfall($awsId)
    {
        // beda setiap alat (biar tidak sama)
        return rand(0, 20) + ($awsId * 2);
    }

    private function generateTemperature($awsId)
    {
        return rand(24, 32) + ($awsId - 1); // alat 1,2,3 beda tipis
    }

    private function generateHumidity($awsId)
    {
        return rand(60, 90) - ($awsId * 2); // makin tinggi aws_id makin rendah
    }
}