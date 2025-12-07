<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AwsStatusLog;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;

class LaporanHarianController extends Controller
{
    public function index(Request $request)
    {
        $tglMulai = $request->tglMulai ?: Carbon::now()->format('Y-m-d');
        $tglAkhir = $request->tglAkhir ?: Carbon::now()->format('Y-m-d');

        $data = AwsStatusLog::with('aws')
            ->whereBetween('waktu', [
                $tglMulai . ' 00:00:00',
                $tglAkhir . ' 23:59:59'
            ])
            ->orderBy('aws_id')
            ->orderBy('waktu')
            ->get();

        $stations = $data->groupBy('aws_id');

        $laporan = [];

        foreach ($stations as $awsId => $logs) {

            $namaAws = $logs->first()->aws->name ?? '-';
            $waktuMati = null;

            foreach ($logs as $log) {

                if ($log->status == 'mati' && !$waktuMati) {
                    $waktuMati = $log->waktu;
                }
                else if ($log->status == 'hidup' && $waktuMati) {

                    $waktuHidup = $log->waktu;

                    $durasi = Carbon::parse($waktuMati)
                        ->diff(Carbon::parse($waktuHidup))
                        ->format('%H jam %I menit %S detik');

                    $laporan[] = [
                        'name'    => $namaAws,
                        'tanggal' => Carbon::parse($waktuMati)->format('d-m-Y'),
                        'mati'    => Carbon::parse($waktuMati)->format('H:i:s'),
                        'hidup'   => Carbon::parse($waktuHidup)->format('H:i:s'),
                        'durasi'  => $durasi,
                    ];

                    $waktuMati = null;
                }
            }
        }

        $laporan = collect($laporan)
            ->sortBy('tanggal')
            ->values();

        $page = request()->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $paginatedLaporan = new LengthAwarePaginator(
            $laporan->slice($offset, $perPage)->values(),
            $laporan->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('report.laporanHarian', [
            'laporan' => $paginatedLaporan,
            'tglMulai' => $tglMulai,
            'tglAkhir' => $tglAkhir,
            'title' => 'Laporan Alat',
        ]);
    }

    public function cetakPdf(Request $request)
    {
        $tglMulai = $request->tglMulai ?: Carbon::now()->format('Y-m-d');
        $tglAkhir = $request->tglAkhir ?: Carbon::now()->format('Y-m-d');

        $data = AwsStatusLog::with('aws')
            ->whereBetween('waktu', [
                $tglMulai . ' 00:00:00',
                $tglAkhir . ' 23:59:59'
            ])
            ->orderBy('aws_id')
            ->orderBy('waktu')
            ->get();

        $grouped = $data->groupBy('aws_id');

        $laporan = [];

        foreach ($grouped as $awsId => $logs) {

            $namaAws = $logs->first()->aws->name ?? '-';
            $waktuMati = null;

            foreach ($logs as $log) {
                if ($log->status == 'mati' && !$waktuMati) {
                    $waktuMati = $log->waktu;
                }
                else if ($log->status == 'hidup' && $waktuMati) {

                    $waktuHidup = $log->waktu;

                    $durasi = Carbon::parse($waktuMati)
                        ->diff(Carbon::parse($waktuHidup))
                        ->format('%H jam %I menit %S detik');

                    $laporan[] = [
                        'name'    => $namaAws,
                        'tanggal' => Carbon::parse($waktuMati)->format('d-m-Y'),
                        'mati'    => Carbon::parse($waktuMati)->format('H:i:s'),
                        'hidup'   => Carbon::parse($waktuHidup)->format('H:i:s'),
                        'durasi'  => $durasi,
                    ];

                    $waktuMati = null;
                }
            }
        }

        $laporan = collect($laporan)
            ->sortBy('tanggal')
            ->values()
            ->all();

        $pdf = Pdf::loadView('report.pdfHarian', [
            'laporan' => $laporan,
            'tglMulai' => $tglMulai,
            'tglAkhir' => $tglAkhir
        ])->setPaper('A4', 'portrait');

        return $pdf->stream('Laporan-Harian-AWS.pdf');
    }
}