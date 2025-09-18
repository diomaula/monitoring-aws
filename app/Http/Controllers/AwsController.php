<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\DataAws;
use App\Models\AwsLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class AwsController extends Controller
{
    public function stations(Request $request)
    {
        $stationsMeta = [
            '5000000031' => ['name' => 'AWS Digi Banyuwangi', 'lat' => -8.214302905669573, 'lng' => 114.35563695303902, 'region' => 'banyuwangi'],
            '3000000007' => ['name' => 'AWS Maritim Ketapang', 'lat' => -8.142126088215901, 'lng' => 114.40021580173305, 'region' => 'banyuwangi'],
            '3000000046' => ['name' => 'AWS Maritim Gilimanuk', 'lat' => -8.161597791585667, 'lng' => 114.43771049574364, 'region' => 'banyuwangi'],
            // '5000000069' => ['name' => 'AWS Lainnya', 'lat' => -8.28, 'lng' => 114.39, 'region' => 'banyuwangi'],
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
                // AwsLog::create([
                //     'station_id' => $id,
                //     'name' => $meta['name'],
                //     'rainfall' => floatval($json['rainfall'] ?? 0)
                // ]);
            }
        }

        // Filter by region jika ada parameter ?region=
        if ($request->has('region')) {
            $data = array_values(array_filter($data, fn($s) => $s['region'] === $request->region));
        }

        return response()->json($data);
    }

    public function index($id)
    {
        $names = [
            '3000000007' => 'AWS Maritim Ketapang',
            '3000000046' => 'AWS Maritim Gilimanuk',
            '5000000031' => 'AWS Digi Banyuwangi',
        ];

        if (!isset($names[$id])) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Wilayah tidak ditemukan'], 404);
            }
            return redirect('dashboard')->with('error', 'Wilayah tidak ditemukan.');
        }

        $response = Http::get("http://202.90.199.132/aws-new/data/station/latest/{$id}");

        if ($response->successful()) {
            $jsonData = $response->json();

            $data = [
                'pancitemp'   => $jsonData['pancitemp'] ?? 0,
                'pancilevel'  => $jsonData['pancilevel'] ?? 0,
                'temp'        => $jsonData['temp'] ?? 0,
                'solrad'      => $jsonData['solrad'] ?? 0,
                'rh'          => $jsonData['rh'] ?? 0,
                'rain'        => $jsonData['rain'] ?? 0,
                'watertemp'   => $jsonData['watertemp'] ?? 0,
                'pressure'    => $jsonData['pressure'] ?? 0,
                'windspeed'   => $jsonData['windspeed'] ?? 0,
                'winddir'     => $jsonData['winddir'] ?? 0,
                'waterlevel'  => $jsonData['waterlevel'] ?? 0,
                'waktu'       => $jsonData['waktu'] ?? null,
            ];

            $isOnline = !empty($jsonData);
        } else {
            $data = [];
            $isOnline = false;
        }

        // 🔥 Jika request API (fetch), balikan JSON
        if (request()->wantsJson()) {
            return response()->json($data);
        }

        // 🔥 Jika request biasa (web), balikan view
        return view('aws.index', [
            'id'     => $id,
            'name'   => $names[$id],
            'data'   => $data,
            'online' => $isOnline,
        ]);
    }


    private function getStatus($data)
    {
        $values = collect($data)->except(['idaws','waktu'])->map(fn($v) => floatval($v));
        return $values->every(fn($v) => $v == 0) ? 'MERAH' : 'HIJAU';
    }

    // 7 hari terakhir
    public function getWeeklyAverage()
    {
        // $start = Carbon::now()->subDays(6);
        // $averages = AwsLog::select(
        //     DB::raw('DATE(created_at) as date'),
        //     DB::raw('AVG(rainfall) as avg_rainfall')
        // )
        // ->where('created_at', '>=', $start)
        // ->groupBy(DB::raw('DATE(created_at)'))
        // ->orderBy('date')
        // ->get();

        // return response()->json($averages);
    }

    public function weeklyMultiParameter()
    {
        $stations = ['5000000069','3000000007','3000000046']; 
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

        return response()->json(array_values($data->toArray())); // <-- pastikan numerik array
    }

    public function getChartData()
    {
        // Ambil data 7 hari terakhir
        $data = DataAws::where('timestamp', '>=', Carbon::now()->subDays(7))
            ->orderBy('timestamp', 'asc')
            ->get();

        // Format untuk chart ApexCharts
        $rainfall = [];
        $temp = [];
        $humidity = [];

        foreach ($data as $row) {
            $rainfall[] = [$row->timestamp->toIso8601String(), (float) $row->rainfall];
            $temp[]     = [$row->timestamp->toIso8601String(), (float) $row->temperature];
            $humidity[] = [$row->timestamp->toIso8601String(), (float) $row->humidity];
        }

        return response()->json([
            'rainfall' => $rainfall,
            'temp'     => $temp,
            'humidity' => $humidity,
        ]);
    }

    
}