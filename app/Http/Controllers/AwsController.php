<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AwsLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class AwsController extends Controller
{
    public function stations(Request $request)
    {
        $stationsMeta = [
            '5000000069' => ['name' => 'AWS Digi Ketapang', 'lat' => -8.142677735101149, 'lng' => 114.40040531090388, 'region' => 'banyuwangi'],
            '3000000007' => ['name' => 'AWS Maritim Banyuwangi/Ketapang', 'lat' => -8.2, 'lng' => 114.37, 'region' => 'banyuwangi'],
            '3000000046' => ['name' => 'AWS Maritim Gilimanuk', 'lat' => -8.161597791585667, 'lng' => 114.43771049574364, 'region' => 'banyuwangi'],
            // '5000000031' => ['name' => 'AWS Lainnya', 'lat' => -8.28, 'lng' => 114.39, 'region' => 'banyuwangi'],
            // 1000000013
        ];

        $data = [];
        foreach ($stationsMeta as $id => $meta) {
            $response = Http::get("http://202.90.199.132/aws-new/data/station/latest/{$id}");
            if ($response->successful()) {
                $json = $response->json();
                $json['status'] = $this->getStatus($json);
                $json['id'] = $id;
                $json['name'] = $meta['name'];
                $json['lat'] = $meta['lat'];
                $json['lng'] = $meta['lng'];
                $json['region'] = $meta['region'];
                $data[] = $json;

                // Simpan ke database (curah hujan = ambil field sesuai API, misal 'rainfall')
                AwsLog::create([
                    'station_id' => $id,
                    'name' => $meta['name'],
                    'rainfall' => floatval($json['rainfall'] ?? 0)
                ]);
            }
        }

        // Filter by region jika ada parameter ?region=
        if ($request->has('region')) {
            $data = array_values(array_filter($data, fn($s) => $s['region'] === $request->region));
        }

        return response()->json($data);
    }

    private function getStatus($data)
    {
        $values = collect($data)->except(['idaws','waktu'])->map(fn($v) => floatval($v));
        return $values->every(fn($v) => $v == 0) ? 'MERAH' : 'HIJAU';
    }

    public function getWeeklyAverage()
    {
        $start = Carbon::now()->subDays(6); // 7 hari terakhir
        $averages = AwsLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('AVG(rainfall) as avg_rainfall')
        )
        ->where('created_at', '>=', $start)
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date')
        ->get();

        return response()->json($averages);
    }

    public function weeklyMultiParameter()
    {
        $stations = ['5000000069','3000000007','3000000046','5000000031']; 
        $days = collect(range(0,6))->map(fn($d) => Carbon::now()->subDays($d)->format('Y-m-d'))->reverse();

        $data = $days->map(function ($date) use ($stations) {
            $rain = 0; $temp = 0; $humid = 0; $count = 0;
            foreach ($stations as $station) {
                $response = Http::get("http://202.90.199.132/aws-new/data/station/latest/{$station}");
                if ($response->successful()) {
                    $json = $response->json();
                    $rain += floatval($json['rainfall'] ?? 0);
                    $temp += floatval($json['temperature'] ?? 0);
                    $humid += floatval($json['humidity'] ?? 0);
                    $count++;
                }
            }
            return [
                'date' => $date,
                'rainfall' => $count ? $rain / $count : 0,
                'temperature' => $count ? $temp / $count : 0,
                'humidity' => $count ? $humid / $count : 0,
            ];
        });

        return response()->json($data);
    }

}
