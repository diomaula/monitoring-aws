<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $awsList = DB::table('aws')->get();
        $reports = [];

        foreach ($awsList as $aws) {
            $data = DB::table('data_aws')
                ->where('aws_id', $aws->id)
                ->whereMonth('timestamp', $month) 
                ->whereYear('timestamp', $year)
                ->select('temperature', 'humidity', 'rainfall', 'wind_speed', 'wind_direction', 'timestamp')
                ->get();

            if ($data->isEmpty()) {
                $reports[] = [
                    'name' => $aws->name,
                    'location' => $aws->location,
                    'temperature_min' => '-',
                    'temperature_max' => '-',
                    'temperature_avg' => '-',
                    'humidity_min' => '-',
                    'humidity_max' => '-',
                    'humidity_avg' => '-',
                    'rainfall_sum' => '-',
                    'wind_speed_min' => '-',
                    'wind_speed_max' => '-',
                    'wind_speed_avg' => '-',
                    'dominant_wind' => '-',
                ];
            } else {
                $reports[] = [
                    'name' => $aws->name,
                    'location' => $aws->location,
                    ...$this->hitungSuhu($data),
                    ...$this->hitungKelembapan($data),
                    ...$this->hitungCurahHujan($data),
                    ...$this->hitungKecepatanAngin($data),
                    'dominant_wind' => $this->hitungArahAnginDominan($data),
                ];
            }
        }

        return view('report.index', compact('reports', 'month', 'year'));
    }

    public function cetakPdf(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $awsList = DB::table('aws')->get();
        $reports = [];

        foreach ($awsList as $aws) {
            $data = DB::table('data_aws')
                ->where('aws_id', $aws->id)
                ->whereMonth('timestamp', $month)
                ->whereYear('timestamp', $year)
                ->select('temperature', 'humidity', 'rainfall', 'wind_speed', 'wind_direction', 'timestamp')
                ->get();

            $reports[] = [
                'name' => $aws->name,
                'location' => $aws->location,
                ...$this->hitungSuhu($data),
                ...$this->hitungKelembapan($data),
                ...$this->hitungCurahHujan($data),
                ...$this->hitungKecepatanAngin($data),
                'dominant_wind' => $this->hitungArahAnginDominan($data),
            ];
        }

        $pdf = PDF::loadView('report.pdf', compact('reports', 'month', 'year'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("Laporan_AWS_{$month}_{$year}.pdf");
    }

    private function hitungSuhu($data)
    {
        return [
            'temperature_min' => round($data->min('temperature'), 1),
            'temperature_max' => round($data->max('temperature'), 1),
            'temperature_avg' => round($data->avg('temperature'), 1),
        ];
    }

    private function hitungKelembapan($data)
    {
        return [
            'humidity_min' => round($data->min('humidity'), 1),
            'humidity_max' => round($data->max('humidity'), 1),
            'humidity_avg' => round($data->avg('humidity'), 1),
        ];
    }

    private function hitungCurahHujan($data)
    {
        if ($data->isEmpty()) {
            return [
                'rainfall_sum' => '-',
                'rainfall_max' => '-',
                'rainfall_max_date' => '-',
                'rainy_days' => '-',
            ];
        }

        $total = round($data->sum('rainfall'), 1);
        $max = $data->max('rainfall');
        $maxData = $data->where('rainfall', $max)->first();

        // ✅ ganti created_at → timestamp
        $rainyDays = $data->filter(fn($row) => $row->rainfall > 0)
            ->groupBy(fn($row) => Carbon::parse($row->timestamp)->toDateString())
            ->count();

        return [
            'rainfall_sum' => $total,
            'rainfall_max' => round($max, 1),
            'rainfall_max_date' => $maxData
                ? Carbon::parse($maxData->timestamp)->translatedFormat('d F Y')
                : '-',
            'rainy_days' => $rainyDays,
        ];
    }

    private function hitungKecepatanAngin($data)
    {
        return [
            'wind_speed_min' => round($data->min('wind_speed'), 1),
            'wind_speed_max' => round($data->max('wind_speed'), 1),
            'wind_speed_avg' => round($data->avg('wind_speed'), 1),
        ];
    }

    private function hitungArahAnginDominan($data)
    {
        if ($data->isEmpty()) return '-';

        $counts = [];
        foreach ($data as $row) {
            $dir = round($row->wind_direction / 22.5) * 22.5;
            $counts[$dir] = ($counts[$dir] ?? 0) + 1;
        }

        $dominant = array_search(max($counts), $counts);

        $directions = [
            0 => 'Utara',
            22.5 => 'Utara-Timur Laut',
            45 => 'Timur Laut',
            67.5 => 'Timur-Timur Laut',
            90 => 'Timur',
            112.5 => 'Timur-Tenggara',
            135 => 'Tenggara',
            157.5 => 'Selatan-Tenggara',
            180 => 'Selatan',
            202.5 => 'Selatan-Barat Daya',
            225 => 'Barat Daya',
            247.5 => 'Barat-Barat Daya',
            270 => 'Barat',
            292.5 => 'Barat-Barat Laut',
            315 => 'Barat Laut',
            337.5 => 'Utara-Barat Laut',
            360 => 'Utara'
        ];

        $closest = 0;
        foreach ($directions as $deg => $name) {
            if (abs($dominant - $deg) < abs($dominant - $closest)) {
                $closest = $deg;
            }
        }

        return 'Bertiup dari ' . $directions[$closest];
    }
}
