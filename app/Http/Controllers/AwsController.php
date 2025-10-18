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

    public function getChartData($code)
    {
        // Cari AWS berdasarkan code
        $aws = Aws::where('code', $code)->first();

        if (!$aws) {
            return response()->json(['error' => 'Wilayah tidak ditemukan'], 404);
        }

        // Ambil data berdasarkan aws_id
        $data = DataAws::where('aws_id', $aws->id)
            ->where('timestamp', '>=', Carbon::now()->subDays(7))
            ->orderBy('timestamp', 'asc')
            ->get();

        $rainfall = [];
        $temp = [];
        $humidity = [];

        foreach ($data as $row) {
            $rainfall[] = [
                "x" => $row->timestamp->toIso8601String(),
                "y" => (float) $row->rainfall,
            ];
            $temp[] = [
                "x" => $row->timestamp->toIso8601String(),
                "y" => (float) $row->temperature,
            ];
            $humidity[] = [
                "x" => $row->timestamp->toIso8601String(),
                "y" => (float) $row->humidity,
            ];
        }

        return response()->json([
            'rainfall' => $rainfall,
            'temp'     => $temp,
            'humidity' => $humidity,
        ]);
    }
}