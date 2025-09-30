<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

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

    public function index()
    {
        // Misal ambil bulan lalu
        $bulanLalu = now()->subMonth();
        $bulanNama = $bulanLalu->translatedFormat('F'); // contoh: Juli
        $tahun = $bulanLalu->year;
        $tanggalRilis = now()->translatedFormat('d F Y'); // contoh: 01 Agustus 2025

        // Dummy data contoh (isi sesuai kebutuhan)
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
            'arahAngin'
        ));
    }

    public function cetakPdf()
    {
        // data dummy contoh (ganti dengan query/database Anda)
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

        $pdf = Pdf::loadView('report.laporanPDF', $data);
        return $pdf->stream('laporan-bulanan.pdf'); // bisa juga ->download()
    }

}
