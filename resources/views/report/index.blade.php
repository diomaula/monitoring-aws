<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Bulanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
  <h2 class="mb-4">📊 Laporan Bulanan</h2>

  {{-- Judul laporan per bulan --}}
  <div class="alert alert-info">
    <strong>Laporan Bulan {{ $bulanNama }} {{ $tahun }}</strong> 
    (Dikeluarkan tanggal {{ $tanggalRilis }})
  </div>

  {{-- Tabel laporan --}}
  <table class="table table-bordered table-striped">
    <thead class="table-primary">
      <tr>
        <th>Parameter</th>
        <th>Nilai</th>
        <th>Satuan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Suhu Minimum</td>
        <td>{{ $suhuMin }}</td>
        <td>°C</td>
      </tr>
      <tr>
        <td>Suhu Maksimum</td>
        <td>{{ $suhuMax }}</td>
        <td>°C</td>
      </tr>
      <tr>
        <td>Suhu Rata-rata</td>
        <td>{{ $suhuAvg }}</td>
        <td>°C</td>
      </tr>
      <tr>
        <td>Kelembapan Rata-rata</td>
        <td>{{ $kelembapanAvg }}</td>
        <td>%</td>
      </tr>
      <tr>
        <td>Curah Hujan</td>
        <td>{{ $curahHujan }}</td>
        <td>mm</td>
      </tr>
    </tbody>
  </table>
</div>
</body>
</html>
