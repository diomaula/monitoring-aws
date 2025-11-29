<!DOCTYPE html>
<html lang="id">

@include('layouts.header')

<body>
  @include('layouts.loading')
  @include('layouts.navbar')
  @include('layouts.sidebar')

  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      vertical-align: middle !important;
      text-align: center !important;
    }

    thead th {
      background-color: #e9f3ff;
      font-weight: 600;
    }

    tbody tr:hover {
      background-color: #f5faff;
    }
  </style>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Laporan AWS</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Laporan AWS</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="card">
        <div class="card-body">
          {{-- ALERT ERROR --}}
          @if($errorMessage)
          <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <strong>Peringatan!</strong> {{ $errorMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          {{-- FORM FILTER --}}
          <form method="GET" action="{{ route('laporan.bulanan') }}" class="row g-3 mt-2 mb-4">
            <div class="col-md-2">
              <label for="month" class="form-label">Bulan</label>
              <select name="month" id="month" class="form-select">
                @for ($m = 1; $m <= 12; $m++)
                  <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                  {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                  </option>
                  @endfor
              </select>
            </div>

            <div class="col-md-2">
              <label for="year" class="form-label">Tahun</label>
              <select name="year" id="year" class="form-select">
                @for ($y = now()->year; $y >= 2020; $y--)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                  {{ $y }}
                </option>
                @endfor
              </select>
            </div>

            <div class="col-md-2 align-self-end">
              <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search"></i> Tampilkan
              </button>
            </div>

            <div class="col-md-2 align-self-end">
              <a href="{{ route('laporan.bulananPdf', ['month' => $month, 'year' => $year]) }}" target="_blank"
                class="btn btn-danger w-100">
                <i class="fas fa-file-pdf"></i> Cetak PDF
              </a>
            </div>
          </form>

          @if(!$errorMessage)
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-center align-middle">
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
                  <td>{{ $r['name'] ?? '-' }}</td>
                  <td>{{ $r['location'] ?? '-' }}</td>

                  <td>{{ $r['temperature_min'] ?? '-' }}</td>
                  <td>{{ $r['temperature_max'] ?? '-' }}</td>
                  <td>{{ $r['temperature_avg'] ?? '-' }}</td>

                  <td>{{ $r['humidity_min'] ?? '-' }}</td>
                  <td>{{ $r['humidity_max'] ?? '-' }}</td>
                  <td>{{ $r['humidity_avg'] ?? '-' }}</td>

                  <td>{{ $r['rainfall_max'] ?? '-' }}</td>
                  <td>{{ $r['rainfall_sum'] ?? '-' }}</td>
                  <td>{{ $r['rainy_days'] ?? '-' }}</td>

                  <td>{{ $r['wind_speed_min'] ?? '-' }}</td>
                  <td>{{ $r['wind_speed_max'] ?? '-' }}</td>
                  <td>{{ $r['wind_speed_avg'] ?? '-' }}</td>

                  <td>{{ $r['dominant_wind'] ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="16" class="text-center text-muted">Tidak ada data untuk bulan ini.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @endif
        </div>
      </div>
    </section>
  </main>

  @include('layouts.footer')
  @include('layouts.script')
</body>

</html>