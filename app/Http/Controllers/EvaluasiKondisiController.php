<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataAws;
use Symfony\Component\Process\Process;

class EvaluasiKondisiController extends Controller
{
    // =============================
    // FUNCTION PREDICT (PYTHON)
    // =============================
    private function predictAnomaly($data)
    {
        $payload = json_encode([
            "aws_id" => (int) $data->aws_id,
            "timestamp" => (string) $data->timestamp,
            "temperature" => (float) $data->temperature,
            "humidity" => (float) $data->humidity,
            "pressure" => (float) $data->pressure,
            "watertemp" => (float) $data->watertemp,
            "waterlevel" => (float) $data->waterlevel,
            "solrad" => (float) $data->solrad,
        ]);

        $process = new Process([
            'python', // ganti jika perlu path full python.exe
            base_path('python_anomali/predict.py')
        ]);

        $process->setInput($payload);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception($process->getErrorOutput());
        }

        return json_decode($process->getOutput(), true);
    }

    // =============================
    // HALAMAN UTAMA
    // =============================
    public function index(Request $request)
    {
        // =============================
        // DEFAULT VALUE (ANTI ERROR)
        // =============================
        $result = [];
        $riwayat = [];
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $contamination = $request->input('contamination_value', 1) / 100;

        // =============================
        // AMBIL SEMUA SCORE
        // =============================
        $allScores = DataAws::whereNotNull('anomaly_score')
            ->pluck('anomaly_score')
            ->sort()
            ->values();

        if ($allScores->count() > 0) {

            // =============================
            // HITUNG THRESHOLD
            // =============================
            $index = floor($contamination * $allScores->count());
            $threshold = $allScores[$index] ?? $allScores->last();

            // =============================
            // CARD (DATA TERBARU)
            // =============================
            $latestData = DataAws::with('aws')
                ->whereNotNull('anomaly_score')
                ->orderBy('timestamp', 'desc')
                ->get()
                ->groupBy('aws_id')
                ->map(fn($items) => $items->first());

            foreach ($latestData as $item) {
                $status = $item->anomaly_score <= $threshold ? 'ANOMALI' : 'NORMAL';

                $result[] = [
                    'aws_id' => $item->aws_id,
                    'nama'   => $item->aws->name ?? '-',
                    'status' => $status,
                    'score'  => $item->anomaly_score,
                    'waktu'  => $item->timestamp,
                ];
            }

            // =============================
            // RIWAYAT
            // =============================
            $riwayat = DataAws::with('aws')
                ->whereMonth('timestamp', $bulan)
                ->whereYear('timestamp', $tahun)
                ->whereNotNull('anomaly_score')
                ->orderBy('timestamp', 'desc')
                ->get()
                ->map(function ($item) use ($threshold) {

                    $status = $item->anomaly_score <= $threshold ? 'ANOMALI' : 'NORMAL';

                    return [
                        'id'     => $item->id,
                        'nama'   => $item->aws->name ?? '-',
                        'tanggal'=> $item->timestamp,
                        'status' => $status,
                        'score'  => $item->anomaly_score,
                    ];
                });
        }

        return view('report.evaluasi-kondisi', compact(
            'result',
            'riwayat',
            'bulan',
            'tahun',
            'contamination'
        ));
    }

    public function indexDetail($aws_id)
    {
        // =============================
        // 1. Ambil data terbaru (anomali)
        // =============================
        $latest = DataAws::with('aws')
            ->where('aws_id', $aws_id)
            ->orderBy('timestamp', 'desc')
            ->first();

        if (!$latest) {
            abort(404);
        }

        // =============================
        // 2. Ambil 6 jam terakhir (untuk chart)
        // =============================
        $history = DataAws::where('aws_id', $aws_id)
            ->orderBy('timestamp', 'desc')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        return view('report.detail-evaluasi-kondisi', compact('latest', 'history'));
    }
}