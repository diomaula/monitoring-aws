<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Harian AWS</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            margin: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 9px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        thead th {
            background-color: #d9e8ff;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <h3 style="text-align: center;">
        Laporan Harian AWS
        @if($pilih_aws)
        - {{ $laporan->firstWhere('aws_id', $pilih_aws)->aws_name ?? '' }}
        @endif
    </h3>
    <p style="text-align:center;">
        {{ \Carbon\Carbon::parse($tglMulai)->translatedFormat('d F Y') }}
        s/d
        {{ \Carbon\Carbon::parse($tglAkhir)->translatedFormat('d F Y') }}
    </p>


    <table>
        <thead class="table-primary">
            <tr>
                @if(!$pilih_aws)
                <th rowspan="2">Nama AWS</th>
                <th rowspan="2">Lokasi</th>
                @endif
                <th rowspan="2">Tanggal</th>
                <th colspan="3">Suhu (°C)</th>
                <th colspan="3">Kelembapan (%)</th>
                <th colspan="3">Curah Hujan (mm)</th>
                <th colspan="3">Kecepatan Angin (m/s)</th>
                <th rowspan="2">Arah Angin Dominan</th>
            </tr>
            <tr class="table-info">
                <th>Min</th>
                <th>Max</th>
                <th>Avg</th>
                <th>Min</th>
                <th>Max</th>
                <th>Avg</th>
                <th>Min</th>
                <th>Max</th>
                <th>Avg</th>
                <th>Tertinggi</th>
                <th>Total</th>
                <th>Hari Hujan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $row)
            <tr>
                @if(!$pilih_aws)
                <td>{{ $row->aws_name }}</td>
                <td>{{ $row->location }}</td>
                @endif
                <td>{{ date('d-m-Y', strtotime($row->date)) }}</td>
                <td>{{ $row->min_temperature }}</td>
                <td>{{ $row->max_temperature }}</td>
                <td>{{ $row->avg_temperature }}</td>
                <td>{{ $row->min_humidity }}</td>
                <td>{{ $row->max_humidity }}</td>
                <td>{{ $row->avg_humidity }}</td>
                <td>{{ $row->rainfall_max }}</td>
                <td>{{ $row->total_rainfall }}</td>
                <td>{{ $row->rainy_days }}</td>
                <td>{{ $row->wind_speed_min }}</td>
                <td>{{ $row->wind_speed_max }}</td>
                <td>{{ $row->wind_speed_avg }}</td>
                <td>{{ $row->dominant_wind_direction }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $pilih_aws ? 18 : 20 }}" style="text-align:center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>