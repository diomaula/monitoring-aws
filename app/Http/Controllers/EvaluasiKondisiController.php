<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataAws;

class EvaluasiKondisiController extends Controller
{
    public function index(Request $request)
    {
        // =============================
        // 1. Default sensitivitas = 1%
        // =============================
        $contamination = $request->input('contamination_value', 1) / 100;

        // =============================
        // 2. Ambil semua anomaly_score untuk threshold
        // =============================
        $allScores = DataAws::whereNotNull('anomaly_score')
            ->pluck('anomaly_score')
            ->sort()
            ->values();

        // Handle jika belum ada data
        if ($allScores->count() == 0) {
            return view('report.evaluasi-kondisi', [
                'result' => [],
                'contamination' => $contamination
            ]);
        }

        // Hitung threshold berdasarkan contamination
        $index = floor($contamination * $allScores->count());
        $threshold = $allScores[$index] ?? $allScores->last();

        // =============================
        // 3. Ambil data terbaru per alat
        // =============================
        $latestData = DataAws::with('aws')
            ->whereNotNull('anomaly_score')
            ->orderBy('timestamp', 'desc')
            ->get()
            ->groupBy('aws_id')
            ->map(function ($items) {
                return $items->first(); // ambil data terbaru
            });

        // =============================
        // 4. Tentukan status tiap alat
        // =============================
        $result = [];

        foreach ($latestData as $item) {

            $status = $item->anomaly_score <= $threshold ? 'ANOMALI' : 'NORMAL';

            $result[] = [
                'aws_id'     => $item->aws_id,
                'nama_alat'  => $item->aws->name ?? 'Tidak Diketahui',
                'status'     => $status,
                'score'      => $item->anomaly_score,
                'waktu'      => $item->timestamp,
            ];
        }

        // =============================
        // 5. Kirim ke view
        // =============================
        return view('report.evaluasi-kondisi', compact('result', 'contamination'));
    }

    public function indexDetail()
    {
        return view('report.detail-evaluasi-kondisi');
    }
}