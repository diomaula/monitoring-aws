<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan AWS</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }

        thead th {
            background-color: #d9e8ff;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .sub-header th {
            background-color: #edf5ff;
            font-weight: normal;
        }
    </style>
</head>

<body>
    <h3>Laporan Bulanan AWS - {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}</h3>

    <table>
        <thead class="table-primary">
            <tr>
                <th rowspan="2">Nama AWS</th>
                <th rowspan="2">Lokasi</th>
                <th colspan="3">Suhu (°C)</th>
                <th colspan="3">Kelembapan (%)</th>
                <th colspan="3">Curah Hujan (mm)</th>
                <th colspan="3">Kecepatan Angin (m/s)</th>
                <th rowspan="2">Arah Angin Dominan</th>
            </tr>

            <tr class="table-info">
                <th>Min</th>
                <th>Max</th>
                <th>Rata-rata</th>

                <th>Min</th>
                <th>Max</th>
                <th>Rata-rata</th>

                <th>Tertinggi</th>
                <th>Total</th>
                <th>Hari Hujan</th>

                <th>Min</th>
                <th>Max</th>
                <th>Rata-rata</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $r)
            <tr>
                <td>{{ $r['name'] }}</td>
                <td>{{ $r['location'] }}</td>

                <td>{{ $r['temperature_min'] }}</td>
                <td>{{ $r['temperature_max'] }}</td>
                <td>{{ $r['temperature_avg'] }}</td>

                <td>{{ $r['humidity_min'] }}</td>
                <td>{{ $r['humidity_max'] }}</td>
                <td>{{ $r['humidity_avg'] }}</td>

                <td>{{ $r['rainfall_max'] }}</td>
                <td>{{ $r['rainfall_sum'] }}</td>
                <td>{{ $r['rainy_days'] }}</td>

                <td>{{ $r['wind_speed_min'] }}</td>
                <td>{{ $r['wind_speed_max'] }}</td>
                <td>{{ $r['wind_speed_avg'] }}</td>
                <td>{{ $r['dominant_wind'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="16" style="text-align:center; color:#666;">Tidak ada data untuk bulan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>