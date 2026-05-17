<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PythonPredictService;

class TestPredictController extends Controller
{
    public function index()
    {
        // ============================
        // DATA DUMMY UNTUK TEST
        // ============================
        $data = [
            'aws_id' => 1,
            'timestamp' => now()->format('Y-m-d H:i:s'),

            'temperature' => 30,
            'humidity' => 80,
            'pressure' => 1008,
            'watertemp' => 27,
            'waterlevel' => 5,
            'solrad' => 300,
        ];

        // ============================
        // KIRIM KE PYTHON
        // ============================
        $result = PythonPredictService::predict($data);

        // ============================
        // TAMPILKAN HASIL
        // ============================
        return response()->json([
            'input' => $data,
            'prediction' => $result
        ]);
    }
}