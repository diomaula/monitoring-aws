<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataAws;
use Symfony\Component\Process\Process;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class EvaluasiKondisiController extends Controller
{
    public function index(Request $request)
    {

        $result = [];
        $riwayat = [];
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $contamination = $request->input('contamination_value', 1) / 100;
        $lastPrediction = DataAws::max('timestamp');

        $allScores = DataAws::whereNotNull('anomaly_score')
            ->pluck('anomaly_score')
            ->sort()
            ->values();

        if ($allScores->count() > 0) {

            // hitung treshold
            $index = floor($contamination * $allScores->count());
            $threshold = $allScores[$index] ?? $allScores->last();

            // card data terbaru
            $latestData = DataAws::with('aws')
                ->whereNotNull('anomaly_score')
                ->select('aws_id')
                ->selectRaw('MAX(timestamp) as latest_time')
                ->groupBy('aws_id')
                ->get();

            $result = [];

            foreach ($latestData as $row) {

                $item = DataAws::with('aws')
                    ->where('aws_id', $row->aws_id)
                    ->where('timestamp', $row->latest_time)
                    ->first();

                if (!$item) continue;

                $status = $item->anomaly_score <= $threshold ? 'ANOMALI' : 'NORMAL';

                $result[] = [
                    'id'     => $item->id,
                    'aws_id' => $item->aws_id,
                    'nama'   => $item->aws->name ?? '-',
                    'status' => $status,
                    'score'  => $item->anomaly_score,
                    'waktu'  => $item->timestamp,
                ];
            }

            $result = collect($result)->sortBy('aws_id')->values();
    
            $riwayat = DataAws::with('aws')
                ->whereNotNull('anomaly_score')
                ->whereMonth('timestamp', $bulan)
                ->whereYear('timestamp', $tahun)
                ->where('anomaly_score', '<=', $threshold) // hanya anomali
                ->orderBy('timestamp', 'desc')
                ->paginate(20)
                ->through(function ($item) {

                    return [
                        'id'       => $item->id,
                        'nama'     => $item->aws->name ?? '-',
                        'tanggal'  => $item->timestamp,
                        'waktu'    => $item->timestamp,
                        'status'   => 'ANOMALI',
                        'score'    => round($item->anomaly_score, 2),
                    ];
                });
        }

        return view('report.evaluasi-kondisi', compact(
            'result',
            'riwayat',
            'bulan',
            'tahun',
            'contamination',
            'lastPrediction'
        ));
    }

    public function indexDetail($id)
    {
        $latest = DataAws::with('aws')->findOrFail($id);

        $history = DataAws::where('aws_id', $latest->aws_id)
            ->where('timestamp', '<=', $latest->timestamp)
            ->orderBy('timestamp', 'desc')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        return view('report.detail-evaluasi-kondisi', compact('latest', 'history'));
    }

    public function pdf(Request $request)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $contamination = $request->input('contamination_value', 1) / 100;

        $allScores = DataAws::whereNotNull('anomaly_score')
            ->pluck('anomaly_score')
            ->sort()
            ->values();

        $riwayat = collect();

        if ($allScores->count() > 0) {

            $index = floor($contamination * $allScores->count());
            $threshold = $allScores[$index] ?? $allScores->last();

            $riwayat = DataAws::with('aws')
                ->whereNotNull('anomaly_score')
                ->whereMonth('timestamp', $bulan)
                ->whereYear('timestamp', $tahun)
                ->where('anomaly_score', '<=', $threshold)
                ->orderBy('timestamp', 'desc')
                ->get()
                ->map(function ($item) {

                    return [
                        'id'       => $item->id,
                        'nama'     => $item->aws->name ?? '-',
                        'tanggal'  => $item->timestamp,
                        'waktu'    => $item->timestamp,
                        'status'   => 'ANOMALI',
                        'score'    => round($item->anomaly_score, 2),
                    ];
                });
        }

        $tglMulai = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tglAkhir = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $pdf = Pdf::loadView('report.pdf-evaluasi-kondisi', compact(
            'riwayat',
            'tglMulai',
            'tglAkhir',
            'bulan',
            'tahun',
            'contamination'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream(
            'Riwayat_Data_Anomali_' .
            Carbon::create($tahun, $bulan)->format('F_Y') .
            '.pdf'
        );
    }
}