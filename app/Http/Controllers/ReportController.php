<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aws;
use App\Models\LaporanHarian;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LapJamExport;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    private function derajatKeArah($degree)
    {
        if ($degree === null) return null;
        $dirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
        $idx = round($degree / 45) % 8;
        return $dirs[$idx];
    }

    public function hitungLaporan($tanggal = null)
    {
        $tanggal = $tanggal ?? Carbon::yesterday()->toDateString();

        // Ambil data per tanggal
        $data = DB::table('data_aws')
            ->whereDate('timestamp', $tanggal)
            ->orderBy('aws_id')
            ->orderBy('timestamp')
            ->get();

        if ($data->isEmpty()) {
            return response()->json([
                'message' => "Tidak ada data untuk tanggal $tanggal",
                'results' => []
            ]);
        }

        $grouped = $data->groupBy('aws_id');
        $results = [];

        foreach ($grouped as $awsId => $records) {

            // Pastikan ada 24 data per jam
            if ($records->count() < 24) {
                $results[] = [
                    'aws_id' => $awsId,
                    'date' => $tanggal,
                    'message' => 'Data tidak lengkap (kurang dari 24 jam), laporan tidak dihitung.'
                ];
                continue;
            }

            // Ambil data valid
            $temps     = $records->pluck('temperature')->filter(fn($v) => $v !== null)->toArray();
            $humidity  = $records->pluck('humidity')->filter(fn($v) => $v !== null)->toArray();
            $pressure  = $records->pluck('pressure')->filter(fn($v) => $v !== null)->toArray();
            $rainfall  = $records->pluck('rainfall')->map(fn($v) => $v ?? 0)->toArray();
            $windSpeed = $records->pluck('wind_speed')->filter(fn($v) => $v !== null)->toArray();

            // Konversi derajat ke kode arah
            $windDir = $records->pluck('wind_direction')
                ->filter(fn($v) => $v !== null)
                ->map(fn($v) => $this->derajatKeArah($v))
                ->toArray();

            // Hitung nilai min, max, avg
            $minTemp = !empty($temps) ? min($temps) : 0;
            $maxTemp = !empty($temps) ? max($temps) : 0;
            $avgTemp = !empty($temps) ? round(array_sum($temps) / count($temps), 2) : 0;

            $minHum = !empty($humidity) ? min($humidity) : 0;
            $maxHum = !empty($humidity) ? max($humidity) : 0;
            $avgHum = !empty($humidity) ? round(array_sum($humidity) / count($humidity), 2) : 0;

            $minPress = !empty($pressure) ? min($pressure) : 0;
            $maxPress = !empty($pressure) ? max($pressure) : 0;
            $avgPress = !empty($pressure) ? round(array_sum($pressure) / count($pressure), 2) : 0;

            $totalRain = array_sum($rainfall);
            $maxRain   = !empty($rainfall) ? max($rainfall) : 0;
            $rainyDays = $totalRain > 0 ? 1 : 0;

            $minWind = !empty($windSpeed) ? min($windSpeed) : 0;
            $maxWind = !empty($windSpeed) ? max($windSpeed) : 0;
            $avgWind = !empty($windSpeed) ? round(array_sum($windSpeed) / count($windSpeed), 2) : 0;

            // Dominant wind direction
            $dominantDir = '-';
            if (!empty($windDir)) {
                $countDir = array_count_values($windDir);
                arsort($countDir);
                $dominantKey = array_key_first($countDir);

                $windDirections = [
                    'N'  => 'Bertiup dari Utara',
                    'NE' => 'Bertiup dari Timur Laut',
                    'E'  => 'Bertiup dari Timur',
                    'SE' => 'Bertiup dari Tenggara',
                    'S'  => 'Bertiup dari Selatan',
                    'SW' => 'Bertiup dari Barat Daya',
                    'W'  => 'Bertiup dari Barat',
                    'NW' => 'Bertiup dari Barat Laut',
                ];

                $dominantDir = $windDirections[$dominantKey] ?? $dominantKey;
            }

            // Simpan ke laporan_harian
            LaporanHarian::updateOrCreate(
                ['aws_id' => $awsId, 'date' => $tanggal],
                [
                    'min_temperature' => $minTemp,
                    'max_temperature' => $maxTemp,
                    'avg_temperature' => $avgTemp,
                    'min_humidity' => $minHum,
                    'max_humidity' => $maxHum,
                    'avg_humidity' => $avgHum,
                    'min_pressure' => $minPress,
                    'max_pressure' => $maxPress,
                    'avg_pressure' => $avgPress,
                    'total_rainfall' => $totalRain,
                    'rainfall_max' => $maxRain,
                    'rainy_days' => $rainyDays,
                    'wind_speed_min' => $minWind,
                    'wind_speed_max' => $maxWind,
                    'wind_speed_avg' => $avgWind,
                    'dominant_wind_direction' => $dominantDir,
                ]
            );

            $results[] = [
                'aws_id' => $awsId,
                'date' => $tanggal,
                'min_temperature' => $minTemp,
                'max_temperature' => $maxTemp,
                'avg_temperature' => $avgTemp,
                'min_humidity' => $minHum,
                'max_humidity' => $maxHum,
                'avg_humidity' => $avgHum,
                'min_pressure' => $minPress,
                'max_pressure' => $maxPress,
                'avg_pressure' => $avgPress,
                'total_rainfall' => $totalRain,
                'rainfall_max' => $maxRain,
                'rainy_days' => $rainyDays,
                'wind_speed_min' => $minWind,
                'wind_speed_max' => $maxWind,
                'wind_speed_avg' => $avgWind,
                'dominant_wind_direction' => $dominantDir,
            ];
        }

        return response()->json($results);
    }


    // Laporan Harian 
    public function lapHarian(Request $request)
    {
        $tglMulai = $request->input('tglMulai', now()->toDateString());
        $tglAkhir   = $request->input('tglAkhir', now()->toDateString());
        $pilih_aws = $request->input('aws_id');

        $aws_list = Aws::orderBy('name')->get();

        if ($tglMulai > $tglAkhir) {
            return view('report.harian', [
                'data' => [],
                'aws_list' => $aws_list,
                'tglMulai' => $tglMulai,
                'tglAkhir' => $tglAkhir,
                'pilih_aws' => $pilih_aws,
                'errorMessage' => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.',
            ]);
        }

        // === GENERATE LAPORAN HARIAN TERLEBIH DAHULU ===
        // Bisa loop per tanggal jika range lebih dari 1 hari
        $current = Carbon::parse($tglMulai);
        $end     = Carbon::parse($tglAkhir);

        while ($current->lte($end)) {
            $this->hitungLaporan($current->toDateString());
            $current->addDay();
        }

        // Ambil data dari tabel laporan_harian
        $data = LaporanHarian::with('aws')
            ->when($pilih_aws, fn($q) => $q->where('aws_id', $pilih_aws))
            ->whereBetween('date', [$tglMulai, $tglAkhir])
            ->orderBy('date', 'desc')
            ->orderBy('aws_id')
            ->paginate(10)
            ->appends([
                'tglMulai' => $tglMulai,
                'tglAkhir' => $tglAkhir,
                'aws_id'   => $pilih_aws
            ]);


        return view('report.harian', [
            'data' => $data,
            'aws_list' => $aws_list,
            'tglMulai' => $tglMulai,
            'tglAkhir' => $tglAkhir,
            'pilih_aws' => $pilih_aws,
            'errorMessage' => null,
        ]);
    }

    public function cetakHarian(Request $request)
    {
        $tglMulai = $request->input('tglMulai', now()->toDateString());
        $tglAkhir   = $request->input('tglAkhir', now()->toDateString());
        $pilih_aws = $request->input('aws_id');

        $laporan = DB::table('laporan_harian')
            ->when($pilih_aws, fn($q) => $q->where('aws_id', $pilih_aws))
            ->whereBetween('date', [$tglMulai, $tglAkhir])
            ->orderBy('date','desc')
            ->orderBy('aws_id')
            ->get();

        foreach ($laporan as $row) {
            $aws = DB::table('aws')->where('id', $row->aws_id)->first();
            $row->aws_name = $aws->name ?? '-';
            $row->location = $aws->location ?? '-';
        }

        $pdf = Pdf::loadView('report.harianPdf', compact('laporan', 'tglMulai', 'tglAkhir', 'pilih_aws'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-harian.pdf');
    }
    // Laporan Bulanan 
    public function lapBulanan(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $namaBulan = Carbon::create($year, $month, 1)->translatedFormat('F');
        $reqTanggal = Carbon::create($year, $month, 1);
        $awalBulanIni = now()->startOfMonth();
        $awalBulanDepan = $awalBulanIni->copy()->addMonth();

        $errorMessage = null;
        $reports = [];

        // Validasi tanggal
        if ($reqTanggal->gt($awalBulanIni)) {
            $errorMessage = "Laporan bulan $namaBulan $year belum tersedia.";
            return view('report.bulanan', compact('reports', 'month', 'year', 'errorMessage'));
        }

        if ($reqTanggal->eq($awalBulanIni) && now()->lt($awalBulanDepan)) {
            $errorMessage = "Laporan bulan $namaBulan $year baru dapat diakses pada "
                . $awalBulanDepan->translatedFormat('d F Y') . ".";
            return view('report.bulanan', compact('reports', 'month', 'year', 'errorMessage'));
        }

        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Ambil data AWS beserta laporan harian bulan ini
        $aws_list = Aws::with(['laporanHarian' => function ($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('date', [$startOfMonth, $endOfMonth]);
        }])->get();

        foreach ($aws_list as $aws) {
            $rows = $aws->laporanHarian;
            if ($rows->isEmpty()) continue;

            // Hitung arah angin dominan (mode)
            $dominantDir = $rows->pluck('dominant_wind_direction')
                ->filter(fn($v) => !empty($v))
                ->countBy()
                ->sortDesc()
                ->keys()
                ->first() ?? '-';

            $reports[] = [
                'name' => $aws->name,
                'location' => $aws->location,
                'temperature_min' => round($rows->min('min_temperature'), 1),
                'temperature_max' => round($rows->max('max_temperature'), 1),
                'temperature_avg' => round($rows->avg('avg_temperature'), 1),
                'humidity_min' => round($rows->min('min_humidity'), 1),
                'humidity_max' => round($rows->max('max_humidity'), 1),
                'humidity_avg' => round($rows->avg('avg_humidity'), 1),
                'pressure_min' => round($rows->min('min_pressure'), 1),
                'pressure_max' => round($rows->max('max_pressure'), 1),
                'pressure_avg' => round($rows->avg('avg_pressure'), 1),
                'rainfall_sum' => round($rows->sum('total_rainfall'), 1),
                'rainfall_max' => round($rows->max('rainfall_max'), 1),
                'rainy_days'   => $rows->sum('rainy_days'),
                'wind_speed_min' => round($rows->min('wind_speed_min'), 1),
                'wind_speed_max' => round($rows->max('wind_speed_max'), 1),
                'wind_speed_avg' => round($rows->avg('wind_speed_avg'), 1),
                'dominant_wind' => $dominantDir,
            ];
        }

        return view('report.bulanan', compact('reports', 'month', 'year', 'errorMessage'));
    }

    //  Cetak Bulanan 
    public function cetakBulanan(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $selectedDate = Carbon::create($year, $month, 1);
        $releaseDate  = $selectedDate->copy()->addMonth()->startOfMonth();

        if (now()->lt($releaseDate)) {
            return back()->with('error', "PDF laporan bulan $month-$year baru dapat diakses pada " .
                $releaseDate->translatedFormat('d F Y'));
        }

        $startOfMonth = $selectedDate->copy()->startOfMonth();
        $endOfMonth = $selectedDate->copy()->endOfMonth();

        $aws_list = Aws::with(['laporanHarian' => function ($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('date', [$startOfMonth, $endOfMonth]);
        }])->get();

        $reports = [];
        foreach ($aws_list as $aws) {
            $rows = $aws->laporanHarian;
            if ($rows->isEmpty()) continue;

            $dominantDir = $rows->pluck('dominant_wind_direction')
                ->filter(fn($v) => !empty($v))
                ->countBy()
                ->sortDesc()
                ->keys()
                ->first() ?? '-';

            $reports[] = [
                'name'            => $aws->name,
                'location'        => $aws->location,
                'temperature_min' => round($rows->min('min_temperature'), 1),
                'temperature_max' => round($rows->max('max_temperature'), 1),
                'temperature_avg' => round($rows->avg('avg_temperature'), 1),
                'humidity_min'    => round($rows->min('min_humidity'), 1),
                'humidity_max'    => round($rows->max('max_humidity'), 1),
                'humidity_avg'    => round($rows->avg('avg_humidity'), 1),
                'pressure_min'    => round($rows->min('min_pressure'), 1),
                'pressure_max'    => round($rows->max('max_pressure'), 1),
                'pressure_avg'    => round($rows->avg('avg_pressure'), 1),
                'rainfall_sum'    => round($rows->sum('total_rainfall'), 1),
                'rainfall_max'    => round($rows->max('rainfall_max'), 1),
                'rainy_days'      => $rows->sum('rainy_days'),
                'wind_speed_min'  => round($rows->min('wind_speed_min'), 1),
                'wind_speed_max'  => round($rows->max('wind_speed_max'), 1),
                'wind_speed_avg'  => round($rows->avg('wind_speed_avg'), 1),
                'dominant_wind'   => $dominantDir,
            ];
        }

        if (empty($reports)) {
            return back()->with('error', "PDF laporan bulan $month-$year belum tersedia.");
        }

        $pdf = Pdf::loadView('report.bulananPdf', compact('reports', 'month', 'year'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("Laporan_AWS_{$month}_{$year}.pdf");
    }

    //  Laporan Data Mentah
    public function lapJam(Request $request)
    {
        $tglMulai = $request->input('tglMulai', now()->toDateString());
        $tglAkhir = $request->input('tglAkhir', now()->toDateString());
        $pilih_aws = $request->input('aws_id');

        $aws_list = Aws::orderBy('name')->get();

        // validasi tgl
        if ($tglMulai > $tglAkhir) {
            return view('report.jam', [
                'laporan'       => collect(),
                'aws_list'      => $aws_list,
                'tglMulai'      => $tglMulai,
                'tglAkhir'      => $tglAkhir,
                'pilih_aws'     => $pilih_aws,
                'errorMessage'  => 'Tanggal mulai tidak boleh lebih besar dari tanggal akhir.',
            ]);
        }

        // query dengan paginate
        $laporan = DB::table('data_aws')
            ->when($pilih_aws, fn($q) => $q->where('aws_id', $pilih_aws))
            ->whereDate('timestamp', '>=', $tglMulai)
            ->whereDate('timestamp', '<=', $tglAkhir)
            ->orderBy('aws_id')
            ->orderBy('timestamp')
            ->paginate(10)
            ->appends([
                'tglMulai' => $tglMulai,
                'tglAkhir' => $tglAkhir,
                'aws_id'   => $pilih_aws
            ]);          


        // tambahkan info AWS ke setiap row
        foreach ($laporan as $row) {
            $aws = DB::table('aws')->where('id', $row->aws_id)->first();
            $row->aws_name = $aws->name ?? '-';
            $row->location = $aws->location ?? '-';
        }

        return view('report.jam', compact('laporan', 'tglMulai', 'tglAkhir', 'pilih_aws', 'aws_list'));
    }


    public function exportLapJam(Request $request)
    {
        $tglMulai = $request->input('tglMulai', now()->toDateString());
        $tglAkhir = $request->input('tglAkhir', now()->toDateString());
        $pilih_aws = $request->input('aws_id'); // kosong = semua AWS

        // Ambil nama AWS jika ada filter
        $namaAws = '';
        if ($pilih_aws) {
            $aws = Aws::find($pilih_aws);
            $namaAws = $aws ? '_' . $aws->name : '';
        }

        // Format tanggal untuk nama file
        $tglMulaiFormat = date('d-m-Y', strtotime($tglMulai));
        $tglAkhirFormat = date('d-m-Y', strtotime($tglAkhir));

        $fileName = "laporan_data_mentah{$namaAws}_{$tglMulaiFormat}_sampai_{$tglAkhirFormat}.xlsx";

        return Excel::download(new \App\Exports\LapJamExport([
            'tglMulai'  => $tglMulai,
            'tglAkhir'  => $tglAkhir,
            'aws_id'    => $pilih_aws
        ]), $fileName);
    }
}
