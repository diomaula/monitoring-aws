<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aws;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanHarianController extends Controller
{
    public function index(Request $request)
    {
        $tglMulai = $request->tglMulai ?: Carbon::now()->format('Y-m-d');
        $tglAkhir = $request->tglAkhir ?: Carbon::now()->format('Y-m-d');

        $awsList = Aws::all();
        $laporan = [];

        foreach ($awsList as $aws) {

            $data = $aws->data()
                ->whereBetween('timestamp', [$tglMulai . ' 00:00:00', $tglAkhir . ' 23:59:59'])
                ->orderBy('timestamp', 'asc')
                ->get();

            if ($data->isEmpty()) continue;

            $grouped = $data->groupBy(function ($item) {
                return Carbon::parse($item->timestamp)->format('Y-m-d');
            });

            foreach ($grouped as $tanggal => $rows) {

                $waktuMatiPertama = null;
                $waktuHidupPertama = null;

                $isMati = false;
                $pernahMati = false;

                foreach ($rows as $row) {

                    $mati = (
                        $row->rain == 0 &&
                        $row->ws == 0 &&
                        $row->wd == 0 &&
                        $row->humidity == 0 &&
                        $row->temperature == 0
                    );

                    if ($mati && !$isMati) {
                        $waktuMatiPertama = $row->timestamp;
                        $isMati = true;
                        $pernahMati = true;
                    }

                    if (!$mati && $isMati && !$waktuHidupPertama) {
                        $waktuHidupPertama = $row->timestamp;
                    }
                }

                if (!$pernahMati) continue;

                $durasi = '-';
                if ($waktuMatiPertama && $waktuHidupPertama) {
                    $durasi = Carbon::parse($waktuMatiPertama)
                        ->diff(Carbon::parse($waktuHidupPertama))
                        ->format('%H jam %I menit');
                }

                $laporan[] = [
                    'name' => $aws->name,
                    'tanggal' => Carbon::parse($tanggal)->format('d-m-Y'),
                    'mati' => $waktuMatiPertama,
                    'hidup' => $waktuHidupPertama,
                    'durasi' => $durasi,
                ];
            }
        }
        $laporan = collect($laporan)->sortByDesc('tanggal')->values()->all();

        return view('report.laporanHarian', compact('laporan', 'tglMulai', 'tglAkhir'));
    }

    public function cetakPdf(Request $request)
    {
        $tglMulai = $request->tglMulai ?: Carbon::now()->format('Y-m-d');
        $tglAkhir = $request->tglAkhir ?: Carbon::now()->format('Y-m-d');

        $awsList = Aws::all();
        $laporan = [];

        foreach ($awsList as $aws) {

            $data = $aws->data()
                ->whereBetween('timestamp', [$tglMulai . ' 00:00:00', $tglAkhir . ' 23:59:59'])
                ->orderBy('timestamp', 'asc')
                ->get();

            if ($data->isEmpty()) continue;

            $grouped = $data->groupBy(function ($item) {
                return Carbon::parse($item->timestamp)->format('Y-m-d');
            });

            foreach ($grouped as $tanggal => $rows) {

                $waktuMatiPertama = null;
                $waktuHidupPertama = null;

                $isMati = false;
                $pernahMati = false;

                foreach ($rows as $row) {

                    $mati = (
                        $row->rain == 0 &&
                        $row->ws == 0 &&
                        $row->wd == 0 &&
                        $row->humidity == 0 &&
                        $row->temperature == 0
                    );

                    if ($mati && !$isMati) {
                        $waktuMatiPertama = $row->timestamp;
                        $isMati = true;
                        $pernahMati = true;
                    }

                    if (!$mati && $isMati && !$waktuHidupPertama) {
                        $waktuHidupPertama = $row->timestamp;
                    }
                }

                if (!$pernahMati) continue;

                $durasi = '-';
                if ($waktuMatiPertama && $waktuHidupPertama) {
                    $durasi = Carbon::parse($waktuMatiPertama)
                        ->diff(Carbon::parse($waktuHidupPertama))
                        ->format('%H jam %I menit');
                }

                $laporan[] = [
                    'name' => $aws->name,
                    'tanggal' => Carbon::parse($tanggal)->format('d-m-Y'),
                    'mati' => $waktuMatiPertama,
                    'hidup' => $waktuHidupPertama,
                    'durasi' => $durasi,
                ];
            }
        }
        $laporan = collect($laporan)->sortByDesc('tanggal')->values()->all();

        $pdf = Pdf::loadView('report.pdfHarian', [
            'laporan' => $laporan,
            'tglMulai' => $tglMulai,
            'tglAkhir' => $tglAkhir,
        ])->setPaper('A4', 'potrait');

        return $pdf->stream('Laporan-Harian-AWS.pdf');
    }
}