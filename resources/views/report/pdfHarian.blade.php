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

        p{
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
    <h3>Laporan Harian Kondisi Alat AWS</h3>
    <p class="text-center">Periode: {{ \Carbon\Carbon::parse($tglMulai)->format('d-m-Y') }} 
   s/d 
   {{ \Carbon\Carbon::parse($tglAkhir)->format('d-m-Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Nama AWS</th>
                <th>Tangal</th>
                <th>Waktu Mati</th>
                <th>Waktu Hidup</th>
                <th>Durasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $r)
            <tr>
                <td>{{ $r['name'] }}</td>
                <td>{{ $r['tanggal'] }}</td>
                <td>{{ $r['mati'] }}</td>
                <td>{{ $r['hidup'] }}</td>
                <td>{{ $r['durasi'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="16" style="text-align:center; color:#666;">Tidak ada data pada rentang tanggal tersebut.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>