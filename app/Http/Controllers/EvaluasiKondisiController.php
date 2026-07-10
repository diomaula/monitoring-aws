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
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);
        $lastPrediction = DataAws::max('timestamp');

        // Data terbaru tiap AWS
        $latestData = DataAws::with('aws')
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

            if (!$item) {
                continue;
            }

            $result[] = [
                'id'     => $item->id,
                'aws_id' => $item->aws_id,
                'nama'   => $item->aws->name ?? '-',
                'status' => $item->status,
                'score'  => $item->anomaly_score,
                'waktu'  => $item->timestamp,
            ];
        }

        $result = collect($result)
            ->sortBy('aws_id')
            ->values();

        // Riwayat anomali
        $riwayat = DataAws::with('aws')
            ->where('status', 'ANOMALI')
            ->whereMonth('timestamp', $bulan)
            ->whereYear('timestamp', $tahun)
            ->orderBy('timestamp', 'desc')
            ->paginate(20)
            ->through(function ($item) {

                return [
                    'id'      => $item->id,
                    'nama'    => $item->aws->name ?? '-',
                    'tanggal' => $item->timestamp,
                    'waktu'   => $item->timestamp,
                    'status'  => $item->status,
                    'score'   => round($item->anomaly_score, 4),
                ];
            });

        return view(
            'report.evaluasi-kondisi',
            compact(
                'result',
                'riwayat',
                'bulan',
                'tahun',
                'lastPrediction'
            )
        );
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

        $riwayat = DataAws::with('aws')
            ->where('status', 'ANOMALI')
            ->whereMonth('timestamp', $bulan)
            ->whereYear('timestamp', $tahun)
            ->orderBy('timestamp', 'desc')
            ->get()
            ->map(function ($item) {

                return [
                    'id'       => $item->id,
                    'nama'     => $item->aws->name ?? '-',
                    'tanggal'  => $item->timestamp,
                    'waktu'    => $item->timestamp,
                    'status'   => $item->status,
                    'score'    => round($item->anomaly_score, 4),
                ];
            });

        $tglMulai = Carbon::create(
            $tahun,
            $bulan,
            1
        )->startOfMonth();

        $tglAkhir = Carbon::create(
            $tahun,
            $bulan,
            1
        )->endOfMonth();

        $pdf = Pdf::loadView(
            'report.pdf-evaluasi-kondisi',
            compact(
                'riwayat',
                'tglMulai',
                'tglAkhir',
                'bulan',
                'tahun'
            )
        );

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream(
            'Riwayat_Data_Anomali_' .
            Carbon::create($tahun, $bulan)->format('F_Y') .
            '.pdf'
        );
    }
}