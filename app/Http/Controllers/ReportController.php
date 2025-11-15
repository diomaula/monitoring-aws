<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aws;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $awsList = Aws::all();
        $reports = [];

        foreach ($awsList as $aws) {
            $data = $aws->data()
                ->whereMonth('timestamp', $month)
                ->whereYear('timestamp', $year)
                ->select('temperature', 'humidity', 'rainfall', 'wind_speed', 'wind_direction', 'timestamp')
                ->get();

            if ($data->isEmpty()) {
                $reports[] = $this->emptyReport($aws);
            } else {
                $reports[] = array_merge(
                    [
                        'name' => $aws->name,
                        'location' => $aws->location,
                    ],
                    $this->hitungSuhu($data),
                    $this->hitungKelembapan($data),
                    $this->hitungCurahHujan($data),
                    $this->hitungKecepatanAngin($data),
                    [
                        'dominant_wind' => $this->hitungArahAnginDominan($data)
                    ]
                );
            }
        }

        return view('report.index', compact('reports', 'month', 'year'));
    }

    public function cetakPdf(Request $request)
    {
        $month = $request->input('month');
        $year  = $request->input('year');

        $awsList = Aws::all();
        $reports = [];

        foreach ($awsList as $aws) {
            $data = $aws->data()
                ->whereMonth('timestamp', $month)
                ->whereYear('timestamp', $year)
                ->select('temperature', 'humidity', 'rainfall', 'wind_speed', 'wind_direction', 'timestamp')
                ->get();

            if ($data->isEmpty()) {
                $reports[] = $this->emptyReport($aws);
            } else {
                $reports[] = array_merge(
                    [
                        'name' => $aws->name,
                        'location' => $aws->location,
                    ],
                    $this->hitungSuhu($data),
                    $this->hitungKelembapan($data),
                    $this->hitungCurahHujan($data),
                    $this->hitungKecepatanAngin($data),
                    [
                        'dominant_wind' => $this->hitungArahAnginDominan($data)
                    ]
                );
            }
        }

        $pdf = Pdf::loadView('report.pdf', compact('reports', 'month', 'year'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("Laporan_AWS_{$month}_{$year}.pdf");
    }

    private function emptyReport($aws)
    {
        return [
            'name' => $aws->name,
            'location' => $aws->location,
            'temperature_min' => '-',
            'temperature_max' => '-',
            'temperature_avg' => '-',
            'humidity_min' => '-',
            'humidity_max' => '-',
            'humidity_avg' => '-',
            'rainfall_sum' => '-',
            'rainfall_max' => '-',
            'rainfall_max_date' => '-',
            'rainy_days' => '-',
            'wind_speed_min' => '-',
            'wind_speed_max' => '-',
            'wind_speed_avg' => '-',
            'dominant_wind' => '-',
        ];
    }

    private function hitungSuhu($data)
    {
        $min = $data->min('temperature');
        $max = $data->max('temperature');
        $avg = $data->avg('temperature');

        return [
            'temperature_min' => is_numeric($min) ? round($min, 1) : '-',
            'temperature_max' => is_numeric($max) ? round($max, 1) : '-',
            'temperature_avg' => is_numeric($avg) ? round($avg, 1) : '-',
        ];
    }

    private function hitungKelembapan($data)
    {
        $min = $data->min('humidity');
        $max = $data->max('humidity');
        $avg = $data->avg('humidity');

        return [
            'humidity_min' => is_numeric($min) ? round($min, 1) : '-',
            'humidity_max' => is_numeric($max) ? round($max, 1) : '-',
            'humidity_avg' => is_numeric($avg) ? round($avg, 1) : '-',
        ];
    }

    private function hitungCurahHujan($data)
    {
        $max = $data->max('rainfall');
        $maxData = $data->where('rainfall', $max)->first();

        return [
            'rainfall_sum' => round($data->sum('rainfall'), 1),
            'rainfall_max' => is_numeric($max) ? round($max, 1) : '-',
            'rainfall_max_date' => $maxData
                ? Carbon::parse($maxData->timestamp)->translatedFormat('d F Y')
                : '-',
            'rainy_days' => $data->filter(fn($row) => is_numeric($row->rainfall) && $row->rainfall > 0)
                ->groupBy(fn($row) => Carbon::parse($row->timestamp)->toDateString())
                ->count(),
        ];
    }

    private function hitungKecepatanAngin($data)
    {
        $min = $data->min('wind_speed');
        $max = $data->max('wind_speed');
        $avg = $data->avg('wind_speed');

        return [
            'wind_speed_min' => is_numeric($min) ? round($min, 1) : '-',
            'wind_speed_max' => is_numeric($max) ? round($max, 1) : '-',
            'wind_speed_avg' => is_numeric($avg) ? round($avg, 1) : '-',
        ];
    }

    private function hitungArahAnginDominan($data)
    {
        $counts = [];

        foreach ($data as $row) {
            if (!is_numeric($row->wind_direction)) continue;

            $dir = round($row->wind_direction / 22.5) * 22.5;
            $counts[$dir] = ($counts[$dir] ?? 0) + 1;
        }

        if (empty($counts)) {
            return '-';
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

        // FIX: closure biasa + type cast agar editor tidak error
        $closest = collect($directions)
            ->keys()
            ->sortBy(function ($value) use ($dominant) {
                return abs($dominant - (float)$value);
            })
            ->first();

        return "Bertiup dari " . $directions[$closest];
    }
}
