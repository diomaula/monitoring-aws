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
      <h1>Laporan Alat</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Laporan Alat</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="card">
        <div class="card-body">
          <form method="GET" action="{{ route('laporanHarian.index') }}" class="row g-3 mt-2 mb-4">
            <div class="col-md-3">
              <label class="form-label">Tanggal Mulai</label>
              <select name="tglMulai" class="form-select">

                  @php
                      $today = now();
                      $endDate = now()->subYears(2);
                      $dates = [];

                      for ($date = $today->copy(); $date->greaterThanOrEqualTo($endDate); $date->subDay()) {
                          $dates[] = $date->format('Y-m-d');
                      }
                  @endphp

                  @foreach ($dates as $date)
        <option value="{{ $date }}"
            {{ $tglMulai == $date ? 'selected' : '' }}>
            {{ \Carbon\Carbon::parse($date)->translatedFormat('d-m-Y') }}
        </option>
    @endforeach
              </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <select name="tglAkhir" class="form-select">

                    @foreach ($dates as $date)
        <option value="{{ $date }}"
            {{ $tglAkhir == $date ? 'selected' : '' }}>
            {{ \Carbon\Carbon::parse($date)->translatedFormat('d-m-Y') }}
        </option>
    @endforeach
                </select>
            </div>

            <div class="col-md-2 align-self-end">
              <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search"></i> Tampilkan
              </button>
            </div>

            <div class="col-md-2 align-self-end">
              <a href="{{ route('laporanHarian.cetak', [
                      'tglMulai' => $tglMulai,
                      'tglAkhir' => $tglAkhir
                  ]) }}" target="_blank" class="btn btn-danger w-100">
                  <i class="fas fa-file-pdf"></i> Cetak PDF
              </a>
            </div>
          </form>

          {{-- =================== TABEL LAPORAN =================== --}}
          <div class="table-responsive">
            <table class="table teble-bordered table-striped">
                <thead class="bg-primary text-white text-center">
                    <tr>
                        <th>Nama AWS</th>
                        <th>Tanggal</th>
                        <th>Waktu Mati</th>
                        <th>Waktu Hidup</th>
                        <th>Durasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['tanggal'] }}</td>
                            <td class="text-danger">
                              {{ $item['mati'] }}
                          </td>

                          <td class="text-success">
                              {{ $item['hidup'] }}
                          </td>
                            <td>{{ $item['durasi'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ada data pada rentang tanggal tersebut.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </main>

  @include('layouts.footer')
  @include('layouts.script')
</body>

</html>