<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    //
    // public function index()
    // {
    //     // Misal ambil bulan lalu
    //     $bulanLalu = now()->subMonth();
    //     $bulanNama = $bulanLalu->translatedFormat('F'); // contoh: Juli
    //     $tahun = $bulanLalu->year;
    //     $tanggalRilis = now()->format('d F Y'); // contoh: 01 Agustus 2025

    //     // Dummy data contoh, nanti bisa diambil dari DB
    //     $suhuMin = 24.5;
    //     $suhuMax = 34.2;
    //     $suhuAvg = 28.7;
    //     $kelembapanAvg = 75;
    //     $curahHujan = 120;

    //     return view('report.index', compact(
    //         'bulanNama',
    //         'tahun',
    //         'tanggalRilis',
    //         'suhuMin',
    //         'suhuMax',
    //         'suhuAvg',
    //         'kelembapanAvg',
    //         'curahHujan'
    //     ));
    // }

    public function index(Request $request)
    {
        // Ambil input dari query string, default = bulan lalu dan tahun sekarang
        $bulan = intval($request->input('bulan', Carbon::now()->subMonth()->month));
        $tahun = intval($request->input('tahun', Carbon::now()->year));

        // Nama bulan 
        $bulanNama = Carbon::create()->month($bulan)->locale('id')->translatedFormat('F');

        // Tanggal rilis
        $tanggalRilis = Carbon::now()->translatedFormat('d F Y');

        // jika bulan yang dipilih > bulan sekarang (atau tahun lebih besar)
        $now = Carbon::now();
        if ($tahun > $now->year || ($tahun == $now->year && $bulan > $now->month)) {
            return view('report.index', [
                'bulan' => $bulan,
                'bulanNama' => $bulanNama,
                'tahun' => $tahun,
                'tanggalRilis' => $tanggalRilis,
                'laporanAda' => false, // laporan tidak ada
            ]);
        }

        // Dummy data 
        $suhuMin = 24.5;
        $suhuMax = 34.2;
        $suhuAvg = 28.7;

        $kelembapanMin = 60;
        $kelembapanMax = 90;
        $kelembapanAvg = 75;

        $tekananMin = 1005.2;
        $tekananMax = 1015.8;
        $tekananAvg = 1010.5;

        $curahHujan = 120.5;

        $kecepatanAngin = 3.5;
        $arahAngin = "Timur Laut";

        return view('report.index', compact(
            'bulan',
            'bulanNama',
            'tahun',
            'tanggalRilis',
            'suhuMin',
            'suhuMax',
            'suhuAvg',
            'kelembapanMin',
            'kelembapanMax',
            'kelembapanAvg',
            'tekananMin',
            'tekananMax',
            'tekananAvg',
            'curahHujan',
            'kecepatanAngin',
            'arahAngin',
        ) + ['laporanAda' => true]);
    }

    public function cetakPdf()
    {
        // data dummy 
        $data = [
            'bulanNama' => now()->subMonth()->translatedFormat('F'),
            'tahun' => now()->subMonth()->year,
            'tanggalRilis' => now()->format('d F Y'),
            'suhuMin' => 22,
            'suhuMax' => 32,
            'suhuAvg' => 27,
            'kelembapanMin' => 65,
            'kelembapanMax' => 92,
            'kelembapanAvg' => 78,
            'tekananMin' => 1005,
            'tekananMax' => 1013,
            'tekananAvg' => 1009,
            'curahHujan' => 220,
            'kecepatanAngin' => 3.5,
            'arahAngin' => 'Timur Laut',
        ];

        $pdf = Pdf::loadView('report.pdf', $data);
        return $pdf->stream('laporan-bulanan.pdf'); 
    }

}