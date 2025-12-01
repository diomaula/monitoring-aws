<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aws;
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

        // Ambil semua log terurut untuk range tanggal
        $data = AwsStatusLog::whereBetween('waktu', [
                $tglMulai . ' 00:00:00',
                $tglAkhir . ' 23:59:59'
            ])
            ->orderBy('station_id')
            ->orderBy('waktu')
            ->get();

        // Group hanya berdasarkan station
        $stations = $data->groupBy('station_id');

        $laporan = [];

        foreach ($stations as $stationId => $logs) {

            $namaAws = $logs->first()->name;
            $waktuMati = null;

            foreach ($logs as $log)
            {
                // Jika status mati → tunggu sampai ada status hidup berikutnya
                if ($log->status == 'mati' && !$waktuMati) {
                    $waktuMati = $log->waktu;
                }

                // Jika status hidup dan sebelumnya mati → simpan 1 periode
                else if ($log->status == 'hidup' && $waktuMati) {

                    $waktuHidup = $log->waktu;

                    // Hitung durasi
                    $durasi = Carbon::parse($waktuMati)
                            ->diff(Carbon::parse($waktuHidup))
                            ->format('%H jam %I menit %S detik');

                    // Tambahkan ke laporan
                    $laporan[] = [
                        'name'    => $namaAws,
                        'tanggal' => Carbon::parse($waktuMati)->format('d-m-Y'),
                        'mati'    => Carbon::parse($waktuMati)->format('H:i:s'),
                        'hidup'   => Carbon::parse($waktuHidup)->format('H:i:s'),
                        'durasi'  => $durasi,
                    ];

                    // reset
                    $waktuMati = null;
                }
            }
        }

        // Urutkan terbaru
        $laporan = collect($laporan)
                    ->sortBy('tanggal')
                    ->values();

        // === PAGINATE MANUAL ===
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

        // Kirim ke view
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

        // Ambil data sesuai filter
        $data = AwsStatusLog::whereBetween('waktu', [
                $tglMulai . ' 00:00:00',
                $tglAkhir . ' 23:59:59'
            ])
            ->orderBy('station_id')
            ->orderBy('waktu', 'asc')
            ->get();

        // Group berdasarkan station
        $grouped = $data->groupBy('station_id');

        $laporan = [];

        foreach ($grouped as $stationId => $logs) {

            $namaAws = $logs->first()->name;
            $waktuMati = null;

            foreach ($logs as $log) {
                if ($log->status == 'mati' && !$waktuMati) {
                    $waktuMati = $log->waktu;
                }
                else if ($log->status == 'hidup' && $waktuMati) {

                    $waktuHidup = $log->waktu;

                    // Hitung durasi
                    $durasi = Carbon::parse($waktuMati)
                            ->diff(Carbon::parse($waktuHidup))
                            ->format('%H jam %I menit %S detik');

                    // Tambahkan ke laporan
                    $laporan[] = [
                        'name'    => $namaAws,
                        'tanggal' => Carbon::parse($waktuMati)->format('d-m-Y'),
                        'mati'    => Carbon::parse($waktuMati)->format('H:i:s'),
                        'hidup'   => Carbon::parse($waktuHidup)->format('H:i:s'),
                        'durasi'  => $durasi,
                    ];

                    // reset
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
        ])->setPaper('A4', 'potrait');

        return $pdf->stream('Laporan-Harian-AWS.pdf');
    }
}