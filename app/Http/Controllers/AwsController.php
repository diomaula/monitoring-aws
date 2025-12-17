<?php

namespace App\Http\Controllers;

use App\Models\Aws;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\DataAws;
use App\Models\AwsLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class AwsController extends Controller
{
    // Dashboard
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

            }
        }

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
                'pancitemp'   => formatNumber($jsonData['pancitemp'] ?? 0,),
                'pancilevel'  => formatNumber($jsonData['pancilevel'] ?? 0),
                'temp'        => formatNumber($jsonData['temp'] ?? 0, true),
                'solrad'      => formatNumber($jsonData['solrad'] ?? 0, true),
                'rh'          => formatNumber($jsonData['rh'] ?? 0),
                'rain'        => formatNumber($jsonData['rain'] ?? 0),
                'watertemp'   => formatNumber($jsonData['watertemp'] ?? 0),
                'pressure'    => formatNumber($jsonData['pressure'] ?? 0, true),
                'windspeed'   => formatNumber($jsonData['windspeed'] ?? 0, true),
                'windspeed_knot' => formatNumber(($jsonData['windspeed'] ?? 0) * 1.94384, true),
                'winddir'     => formatNumber($jsonData['winddir'] ?? 0),
                'waterlevel'  => formatNumber($jsonData['waterlevel'] ?? 0, true),
                'waktu'       => $jsonData['waktu'] ?? null,
            ];

            $isOnline = !empty($jsonData);
        } else {
            $data = [];
            $isOnline = false;
        }

        // Jika request API (fetch), balikan JSON
        if (request()->wantsJson()) {
            return response()->json($data);
        }

        // Jika request biasa (web), balikan view
        return view('aws.index', [
            'id'     => $id,
            'name'   => $names[$id],
            'data'   => $data,
            'online' => $isOnline,
            'title' => $names[$id],
        ]);
    }

    private function getStatus($data)
    {
        $values = collect($data)->except(['idaws', 'waktu'])->map(fn($v) => floatval($v));
        return $values->every(fn($v) => $v == 0) ? 'MERAH' : 'HIJAU';
    }

    public function getChartData($code)
    {
        // Cari AWS berdasarkan code
        $aws = Aws::where('code', $code)->first();

        if (!$aws) {
            return response()->json(['error' => 'Wilayah tidak ditemukan'], 404);
        }

        // Ambil data berdasarkan aws_id (hanya tiap 3 jam)
        $data = DataAws::where('aws_id', $aws->id)
            ->where('timestamp', '>=', Carbon::now()->subDays(7))
            ->whereRaw('HOUR(`timestamp`) % 3 = 0')
            ->orderBy('timestamp', 'asc')
            ->get();

        $rainfall = [];
        $temp = [];
        $humidity = [];
        $wind_data = [];

        // Arah utama untuk windrose
        $directions = [
            'N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'
        ];

        // Siapkan array untuk menghitung total dan jumlah
        $windrose_data = [];
        foreach ($directions as $dir) {
            $windrose_data[$dir] = ['total_speed' => 0, 'count' => 0];
        }

        // Mapping arah teks ke derajat
        $direction_map = [
            'N' => 0, 'NNE' => 22.5, 'NE' => 45, 'ENE' => 67.5,
            'E' => 90, 'ESE' => 112.5, 'SE' => 135, 'SSE' => 157.5,
            'S' => 180, 'SSW' => 202.5, 'SW' => 225, 'WSW' => 247.5,
            'W' => 270, 'WNW' => 292.5, 'NW' => 315, 'NNW' => 337.5,
        ];

        foreach ($data as $row) {
            $rainfall[] = [
                "x" => $row->timestamp->toIso8601String(),
                "y" => round((float) $row->rainfall, 2), 
            ];
            $temp[] = [
                "x" => $row->timestamp->toIso8601String(),
                "y" => round((float) $row->temperature, 2),
            ];
            $humidity[] = [
                "x" => $row->timestamp->toIso8601String(),
                "y" => round((float) $row->humidity, 2), 
            ];

            // Konversi arah angin
            $dirValue = null;
            if (is_numeric($row->wind_direction)) {
                $dirValue = (float)$row->wind_direction;
            } else {
                $dirValue = $direction_map[strtoupper(trim($row->wind_direction))] ?? null;
            }

            $wind_data[] = [
                "time"  => $row->timestamp->toIso8601String(),
                "dir"   => $dirValue,
                "speed" => (float)$row->wind_speed,
            ];
            // Kelompokkan ke 8 arah utama
            if ($dirValue !== null) {
                if ($dirValue >= 337.5 || $dirValue < 22.5) $sector = 'N';
                elseif ($dirValue < 67.5) $sector = 'NE';
                elseif ($dirValue < 112.5) $sector = 'E';
                elseif ($dirValue < 157.5) $sector = 'SE';
                elseif ($dirValue < 202.5) $sector = 'S';
                elseif ($dirValue < 247.5) $sector = 'SW';
                elseif ($dirValue < 292.5) $sector = 'W';
                else $sector = 'NW';

                $windrose_data[$sector]['total_speed'] += (float)$row->wind_speed;
                $windrose_data[$sector]['count']++;
            }
        }

        // Hitung rata-rata kecepatan tiap arah
        $windrose = [];
        foreach ($windrose_data as $dir => $values) {
            $windrose[$dir] = $values['count'] > 0
                ? round($values['total_speed'] / $values['count'], 2)
                : 0;
        }

        return response()->json([
            'rainfall' => $rainfall,
            'temp'     => $temp,
            'humidity' => $humidity,
            'wind_data' => $wind_data,
            'windrose' =>$windrose,
        ]);
    }
}
