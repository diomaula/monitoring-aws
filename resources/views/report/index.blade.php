<!DOCTYPE html>
<html lang="id">

@include('layouts.header')

<body>
  @include('layouts.loading')
  @include('layouts.navbar')
  @include('layouts.sidebar')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Laporan Bulanan</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
          <li class="breadcrumb-item active">Laporan</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <div class="col-lg-12">

          {{-- Card Filter --}}
          <div class="card mb-4 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">
                <i class="fas fa-filter text-primary"></i> Filter Laporan
              </h5>
              <form action="{{ route('laporan.index') }}" method="GET" class="row g-2 align-items-end">
                @php
                $selectedBulan = $bulan ?? \Carbon\Carbon::now()->subMonth()->month;
                $selectedTahun = $tahun ?? \Carbon\Carbon::now()->year;
                @endphp

                <div class="col-md-4">
                  <label for="bulan" class="form-label">Pilih Bulan</label>
                  <select name="bulan" id="bulan" class="form-select">
                    @for ($i = 1; $i <= 12; $i++)
                      <option value="{{ $i }}" {{ $i == $selectedBulan ? 'selected' : '' }}>
                      {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                      </option>
                      @endfor
                  </select>
                </div>

                <div class="col-md-4">
                  <label for="tahun" class="form-label">Pilih Tahun</label>
                  <input
                    type="number"
                    name="tahun"
                    id="tahun"
                    value="{{ $selectedTahun }}"
                    class="form-control"
                    min="2000"
                    max="{{ now()->year }}">
                </div>

                <div class="col-md-4 d-grid">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tampilkan
                  </button>
                </div>
              </form>
            </div>
          </div>
          {{-- End Card Filter --}}

          {{-- Card Laporan --}}
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">
                <i class="fas fa-chart-bar text-success"></i> Laporan Bulanan
              </h5>

              {{-- Info laporan --}}
              @if($laporanAda)
              <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                  <strong>Laporan Bulan {{ $bulanNama }} {{ $tahun }}</strong>
                  (Dikeluarkan tanggal {{ $tanggalRilis }})
                </div>
              </div>
              @else
              <div class="alert alert-warning d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                  Laporan untuk bulan {{ $bulanNama }} {{ $tahun }} belum tersedia.
                </div>
              </div>
              @endif

              {{-- Tampilkan hanya jika laporan ada --}}
              @if($laporanAda)

              {{-- Tombol cetak PDF --}}
              <a href="{{ route('laporan.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                class="btn btn-danger mb-3" target="_blank">
                <i class="fas fa-print"></i> Cetak PDF
              </a>

              {{-- Tabel laporan --}}
              <table class="table table-bordered table-striped table-hover">
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
                    <td>Kelembapan Minimum</td>
                    <td>{{ $kelembapanMin }}</td>
                    <td>%</td>
                  </tr>
                  <tr>
                    <td>Kelembapan Maksimum</td>
                    <td>{{ $kelembapanMax }}</td>
                    <td>%</td>
                  </tr>
                  <tr>
                    <td>Kelembapan Rata-rata</td>
                    <td>{{ $kelembapanAvg }}</td>
                    <td>%</td>
                  </tr>
                  <tr>
                    <td>Tekanan Minimum</td>
                    <td>{{ $tekananMin }}</td>
                    <td>hPa</td>
                  </tr>
                  <tr>
                    <td>Tekanan Maksimum</td>
                    <td>{{ $tekananMax }}</td>
                    <td>hPa</td>
                  </tr>
                  <tr>
                    <td>Tekanan Rata-rata</td>
                    <td>{{ $tekananAvg }}</td>
                    <td>hPa</td>
                  </tr>
                  <tr>
                    <td>Total Curah Hujan</td>
                    <td>{{ $curahHujan }}</td>
                    <td>mm</td>
                  </tr>
                  <tr>
                    <td>Kecepatan Angin Rata-rata</td>
                    <td>{{ $kecepatanAngin }}</td>
                    <td>m/s</td>
                  </tr>
                  <tr>
                    <td>Arah Angin Dominan</td>
                    <td>{{ $arahAngin }}</td>
                    <td>-</td>
                  </tr>
                </tbody>
              </table>

              @endif

            </div>
          </div>
          {{-- End Card Laporan --}}

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  @include('layouts.footer')

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="fas fa-arrow-up"></i>
  </a>

  @include('layouts.script')
</body>

</html>