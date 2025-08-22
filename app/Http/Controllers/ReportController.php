<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
    public function index()
    {
        // Misal ambil bulan lalu
        $bulanLalu = now()->subMonth();
        $bulanNama = $bulanLalu->translatedFormat('F'); // contoh: Juli
        $tahun = $bulanLalu->year;
        $tanggalRilis = now()->format('d F Y'); // contoh: 01 Agustus 2025

        // Dummy data contoh, nanti bisa diambil dari DB
        $suhuMin = 24.5;
        $suhuMax = 34.2;
        $suhuAvg = 28.7;
        $kelembapanAvg = 75;
        $curahHujan = 120;

        return view('report.index', compact(
            'bulanNama',
            'tahun',
            'tanggalRilis',
            'suhuMin',
            'suhuMax',
            'suhuAvg',
            'kelembapanAvg',
            'curahHujan'
        ));
    }

}
