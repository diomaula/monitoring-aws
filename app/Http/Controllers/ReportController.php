<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aws;
use App\Models\LaporanHarian;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $namaBulan = Carbon::create($year, $month, 1)->translatedFormat('F');
        $reqTanggal = Carbon::create($year, $month, 1);
        $awalBulanIni = now()->startOfMonth();
        $awalBulanDepan = $awalBulanIni->copy()->addMonth();

        $errorMessage = null;
        $reports = [];

        // BLOKIR BULAN DEPAN / BELUM TERJADI
        if ($reqTanggal->gt($awalBulanIni)) {
            $errorMessage = "Laporan bulan $namaBulan $year belum tersedia.";
            return view('report.index', compact('reports', 'month', 'year', 'errorMessage'));
        }

        // BLOKIR BULAN INI SEBELUM RILIS
        if ($reqTanggal->eq($awalBulanIni) && now()->lt($awalBulanDepan)) {
            $errorMessage = "Laporan bulan $namaBulan $year baru dapat diakses pada "
                . $awalBulanDepan->translatedFormat('d F Y') . ".";
            return view('report.index', compact('reports', 'month', 'year', 'errorMessage'));
        }

        // 🔥 HITUNG ARAH ANGIN DOMINAN MENGGUNAKAN MODE
        $dominantWind = DB::table('laporan_harian')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('dominant_wind_direction')
            ->where('dominant_wind_direction', '!=', '')
            ->select('aws_id', 'dominant_wind_direction')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('aws_id', 'dominant_wind_direction')
            ->orderBy('total', 'DESC')
            ->get()
            ->groupBy('aws_id')
            ->map(fn($g) => $g->first()->dominant_wind_direction);


        // 🔥 QUERY UTAMA — NILAI 0 DIABAIKAN MENGGUNAKAN NULLIF
        $laporan = DB::table('laporan_harian')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('aws_id')
            ->selectRaw("
            aws_id,

            MIN(NULLIF(min_temperature, 0)) as min_temp,
            MAX(NULLIF(max_temperature, 0)) as max_temp,
            AVG(NULLIF(avg_temperature, 0)) as avg_temp,

            MIN(NULLIF(min_humidity, 0)) as min_humidity,
            MAX(NULLIF(max_humidity, 0)) as max_humidity,
            AVG(NULLIF(avg_humidity, 0)) as avg_humidity,

            MIN(NULLIF(min_pressure, 0)) as min_pressure,
            MAX(NULLIF(max_pressure, 0)) as max_pressure,
            AVG(NULLIF(avg_pressure, 0)) as avg_pressure,

            SUM(total_rainfall) as rainfall_sum,
            MAX(NULLIF(rainfall_max, 0)) as rainfall_max,
            SUM(rainy_days) as rainy_days,

            MIN(NULLIF(wind_speed_min, 0)) as wind_speed_min,
            MAX(NULLIF(wind_speed_max, 0)) as wind_speed_max,
            AVG(NULLIF(wind_speed_avg, 0)) as wind_speed_avg
        ")->get();

        foreach ($laporan as $row) {
            $aws = Aws::find($row->aws_id);

            $reports[] = [
                'name' => $aws->name ?? '-',
                'location' => $aws->location ?? '-',

                'temperature_min' => round($row->min_temp, 1),
                'temperature_max' => round($row->max_temp, 1),
                'temperature_avg' => round($row->avg_temp, 1),

                'humidity_min' => round($row->min_humidity, 1),
                'humidity_max' => round($row->max_humidity, 1),
                'humidity_avg' => round($row->avg_humidity, 1),

                'pressure_min' => round($row->min_pressure, 1),
                'pressure_max' => round($row->max_pressure, 1),
                'pressure_avg' => round($row->avg_pressure, 1),

                'rainfall_max' => round($row->rainfall_max, 1),
                'rainfall_sum' => round($row->rainfall_sum, 1),
                'rainy_days'   => $row->rainy_days,

                'wind_speed_min' => round($row->wind_speed_min, 1),
                'wind_speed_max' => round($row->wind_speed_max, 1),
                'wind_speed_avg' => round($row->wind_speed_avg, 1),

                'dominant_wind' => $dominantWind[$row->aws_id] ?? '-',
            ];
        }

        return view('report.index', compact('reports', 'month', 'year', 'errorMessage'));
    }


    public function cetakPdf(Request $request)
    {
        $month = $request->input('month');
        $year  = $request->input('year');

        $selectedDate = Carbon::create($year, $month, 1);
        $releaseDate  = $selectedDate->copy()->addMonth()->startOfMonth();

        if (now()->lt($releaseDate)) {
            return back()->with('error', "PDF laporan bulan $month-$year baru dapat diakses pada " .
                $releaseDate->translatedFormat('d F Y'));
        }

        // Arah angin dominan (mode)
        $dominantWind = DB::table('laporan_harian')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotNull('dominant_wind_direction')
            ->where('dominant_wind_direction', '!=', '')
            ->select('aws_id', 'dominant_wind_direction')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('aws_id', 'dominant_wind_direction')
            ->orderBy('total', 'DESC')
            ->get()
            ->groupBy('aws_id')
            ->map(fn($g) => $g->first()->dominant_wind_direction);

        // Data bulanan (nilai 0 diabaikan NULLIF)
        $laporan = DB::table('laporan_harian')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupBy('aws_id')
            ->selectRaw("
            aws_id,
            MIN(NULLIF(min_temperature, 0)) as min_temp,
            MAX(NULLIF(max_temperature, 0)) as max_temp,
            AVG(NULLIF(avg_temperature, 0)) as avg_temp,
            MIN(NULLIF(min_humidity, 0)) as min_humidity,
            MAX(NULLIF(max_humidity, 0)) as max_humidity,
            AVG(NULLIF(avg_humidity, 0)) as avg_humidity,
            MIN(NULLIF(min_pressure, 0)) as min_pressure,
            MAX(NULLIF(max_pressure, 0)) as max_pressure,
            AVG(NULLIF(avg_pressure, 0)) as avg_pressure,
            SUM(total_rainfall) as rainfall_sum,
            MAX(NULLIF(rainfall_max, 0)) as rainfall_max,
            SUM(rainy_days) as rainy_days,
            MIN(NULLIF(wind_speed_min, 0)) as wind_speed_min,
            MAX(NULLIF(wind_speed_max, 0)) as wind_speed_max,
            AVG(NULLIF(wind_speed_avg, 0)) as wind_speed_avg
        ")->get();

        $reports = [];
        foreach ($laporan as $row) {
            $aws = Aws::find($row->aws_id);

            $reports[] = [
                'name' => $aws->name ?? '-',
                'location' => $aws->location ?? '-',
                'temperature_min' => round($row->min_temp, 1),
                'temperature_max' => round($row->max_temp, 1),
                'temperature_avg' => round($row->avg_temp, 1),
                'humidity_min' => round($row->min_humidity, 1),
                'humidity_max' => round($row->max_humidity, 1),
                'humidity_avg' => round($row->avg_humidity, 1),
                'pressure_min' => round($row->min_pressure, 1),
                'pressure_max' => round($row->max_pressure, 1),
                'pressure_avg' => round($row->avg_pressure, 1),
                'rainfall_max' => round($row->rainfall_max, 1),
                'rainfall_sum' => round($row->rainfall_sum, 1),
                'rainy_days'   => $row->rainy_days,
                'wind_speed_min' => round($row->wind_speed_min, 1),
                'wind_speed_max' => round($row->wind_speed_max, 1),
                'wind_speed_avg' => round($row->wind_speed_avg, 1),
                'dominant_wind' => $dominantWind[$row->aws_id] ?? '-',
            ];
        }

        $pdf = Pdf::loadView('report.pdf', compact('reports', 'month', 'year'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("Laporan_AWS_{$month}_{$year}.pdf");
    }
}
