<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aws;
use App\Models\AwsStatusLog;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
                    ->sortByDesc('tanggal')
                    ->values()
                    ->all();

        return view('report.laporanHarian', compact('laporan', 'tglMulai', 'tglAkhir'));
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
            // $tanggal = Carbon::parse($entries->first()->waktu)->format('d-m-Y');
            $waktuMati = null;

            // $waktuMatiPertama = null;
            // $waktuHidupPertama = null;

            foreach ($logs as $log) {

                // Cari waktu mati pertama
                // if ($log->status == 'mati' && !$waktuMatiPertama) {
                //     $waktuMatiPertama = $log->waktu;
                // }
                if ($log->status == 'mati' && !$waktuMati) {
                    $waktuMati = $log->waktu;
                }

                // Cari waktu hidup pertama setelah mati
                // if ($waktuMatiPertama && !$waktuHidupPertama && $log->status == 'hidup') {
                //     $waktuHidupPertama = $log->waktu;
                // }
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

            // Hitung durasi
            // $durasi = '-';
            // if ($waktuMatiPertama && $waktuHidupPertama) {
            //     $durasi = Carbon::parse($waktuMatiPertama)
            //                 ->diff(Carbon::parse($waktuHidupPertama))
            //                 ->format('%H jam %I menit %S detik');
            // }

            // $laporan[] = [
            //     'station_id' => $stationId,
            //     'name'       => $namaAws,
            //     'tanggal'    => $tanggal,
            //     'mati'       => $waktuMatiPertama ? Carbon::parse($waktuMatiPertama)->format('H:i:s') : '-',
            //     'hidup'      => $waktuHidupPertama ? Carbon::parse($waktuHidupPertama)->format('H:i:s') : '-',
            //     'durasi'     => $durasi,
            // ];
        }

        // Urutkan laporan paling baru di atas
        // $laporan = collect($laporan)->sortByDesc('tanggal')->values()->all();
        $laporan = collect($laporan)
                    ->sortByDesc('tanggal')
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