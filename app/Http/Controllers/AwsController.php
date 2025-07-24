<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class AwsController extends Controller
{
    public function index()
    {
        $stations = [
            '5000000069',
            '3000000007',
            '3000000046',
            '5000000031'
        ];

        $data = [];
        foreach ($stations as $station) {
            $response = Http::get("http://202.90.199.132/aws-new/data/station/latest/{$station}");
            if ($response->successful()) {
                $json = $response->json();
                $json['status'] = $this->getStatus($json);
                $data[] = $json;
            }
        }

        return view('aws', compact('data'));
    }

    public function api()
    {
        $stations = [
            '5000000069',
            '3000000007',
            '3000000046',
            '5000000031'
        ];

        $data = [];
        foreach ($stations as $station) {
            $response = Http::get("http://202.90.199.132/aws-new/data/station/latest/{$station}");
            if ($response->successful()) {
                $json = $response->json();
                $json['status'] = $this->getStatus($json);
                $data[] = $json;
            }
        }
        return response()->json($data);
    }

    private function getStatus($data)
    {
        // Ambil semua nilai kecuali idaws & waktu
        $values = collect($data)->except(['idaws','waktu'])->map(fn($v) => floatval($v));
        $allZero = $values->every(fn($v) => $v == 0);
        return $allZero ? 'MERAH' : 'HIJAU';
    }
}
