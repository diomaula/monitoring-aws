<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Support\Facades\DB;
use App\Models\Aws;

class LapJamExport implements FromArray
{
    protected $tglMulai;
    protected $tglAkhir;
    protected $aws_id;
    protected $aws_name;

    public function __construct($params)
    {
        $this->tglMulai = $params['tglMulai'];
        $this->tglAkhir = $params['tglAkhir'];
        $this->aws_id = $params['aws_id'];

        if ($this->aws_id) {
            $aws = Aws::find($this->aws_id);
            $this->aws_name = $aws ? $aws->name : 'AWS';
        } else {
            $this->aws_name = 'Semua AWS';
        }
    }

    public function array(): array
    {
        // Header kolom
        $columns = [
            'Tanggal',
            'Jam',
            'Temperature (°C)',
            'Humidity (%)',
            'Pressure (hPa)',
            'Rainfall (mm)',
            'Wind Speed (m/s)',
            'Wind Direction',
            'Panci Temp',
            'Panci Level',
            'Solar Radiation',
            'Water Temp',
            'Water Level'
        ];

        if (!$this->aws_id) {
            $columns = array_merge(['AWS', 'Lokasi'], $columns);
        }

        // Ambil data
        $laporan = DB::table('data_aws as d')
            ->leftJoin('aws as a', 'd.aws_id', '=', 'a.id')
            ->select(
                'a.name as aws_name',
                'a.location',
                'd.timestamp',
                'd.temperature',
                'd.humidity',
                'd.pressure',
                'd.rainfall',
                'd.wind_speed',
                'd.wind_direction',
                'd.pancitemp',
                'd.pancilevel',
                'd.solrad',
                'd.watertemp',
                'd.waterlevel'
            )
            ->when($this->aws_id, fn($q) => $q->where('d.aws_id', $this->aws_id))
            ->whereDate('d.timestamp', '>=', $this->tglMulai)
            ->whereDate('d.timestamp', '<=', $this->tglAkhir)
            ->orderBy('d.aws_id')
            ->orderBy('d.timestamp')
            ->get()
            ->map(function ($row) {
                $data = [
                    date('d-m-Y', strtotime($row->timestamp)),
                    date('H:i', strtotime($row->timestamp)),
                    $row->temperature ?? '-',
                    $row->humidity ?? '-',
                    $row->pressure ?? '-',
                    $row->rainfall ?? '-',
                    $row->wind_speed ?? '-',
                    $row->wind_direction ?? '-',
                    $row->pancitemp ?? '-',
                    $row->pancilevel ?? '-',
                    $row->solrad ?? '-',
                    $row->watertemp ?? '-',
                    $row->waterlevel ?? '-',
                ];

                if (!$this->aws_id) {
                    $data = array_merge([
                        $row->aws_name ?? '-',
                        $row->location ?? '-'
                    ], $data);
                }

                return $data;
            })->toArray();

        // Gabungkan nama AWS di baris pertama + header kolom + data
        $final = [];
        $final[] = [$this->aws_name]; // baris 1 nama AWS
        $final[] = $columns;          // baris 2 header kolom
        $final = array_merge($final, $laporan); // baris 3 dst data

        return $final;
    }
}
