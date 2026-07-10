<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Data Anomali</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        h3 {
            text-align: center;
            margin-bottom: 5px;
        }

        p {
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
    </style>
</head>

<body>

    <h3>Laporan Riwayat Data Anomali AWS</h3>

    <p>
        Periode:
        {{ \Carbon\Carbon::parse($tglMulai)->format('d-m-Y') }}
        s/d
        {{ \Carbon\Carbon::parse($tglAkhir)->format('d-m-Y') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama AWS</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Score</th>
            </tr>
        </thead>

        <tbody>
            @forelse($riwayat as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item['nama'] }}</td>
                <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($item['waktu'])->format('H:i:s') }}</td>
                <td>{{ $item['status'] }}</td>
                <td>{{ $item['score'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; color:#666;">
                    Tidak ada data anomali pada rentang tanggal tersebut.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>