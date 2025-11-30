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

        .card {
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
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
            <h1>
                Laporan Harian AWS
                @if($pilih_aws)
                - {{ $aws_list->firstWhere('id', $pilih_aws)->name ?? '' }}
                @endif
            </h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Laporan Harian AWS</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="card">
                <div class="card-body">

                    {{-- ALERT ERROR --}}
                    @if(isset($errorMessage) && $errorMessage)
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <strong>Peringatan!</strong> {{ $errorMessage }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form method="GET" action="{{ route('laporan.harian') }}" class="row g-3 mt-2 mb-4">

                        <div class="row g-3 mb-2">
                            <div class="col-md-2">
                                <label for="tglMulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" id="tglMulai" name="tglMulai" class="form-control" value="{{ $tglMulai }}">
                            </div>

                            <div class="col-md-2">
                                <label for="tglAkhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" id="tglAkhir" name="tglAkhir" class="form-control" value="{{ $tglAkhir }}">
                            </div>

                            <div class="col-md-3">
                                <label for="aws_id" class="form-label">Pilih AWS</label>
                                <select name="aws_id" id="aws_id" class="form-control">
                                    <option value="">Semua AWS</option>
                                    @foreach($aws_list as $aws)
                                    <option value="{{ $aws->id }}" {{ $aws->id == $pilih_aws ? 'selected' : '' }}>
                                        {{ $aws->name }} - {{ $aws->location }}
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
                                <a href="{{ route('laporan.harianPdf', ['tglMulai'=>$tglMulai, 'tglAkhir'=>$tglAkhir, 'aws_id'=>$pilih_aws]) }}"
                                    target="_blank" class="btn btn-danger w-100">
                                    <i class="fas fa-file-pdf"></i> Cetak PDF
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover text-center align-middle">
                            <thead class="table-primary">
                                <tr>
                                    @if(!$pilih_aws)
                                    <th rowspan="2">Nama AWS</th>
                                    <th rowspan="2">Lokasi</th>
                                    @endif
                                    <th rowspan="2">Tanggal</th>
                                    <th colspan="3">Suhu (°C)</th>
                                    <th colspan="3">Kelembapan (%)</th>
                                    <th colspan="3">Tekanan Udara (hPa)</th>
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
                                    <th>Min</th>
                                    <th>Max</th>
                                    <th>Avg</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $row)
                                <tr>
                                    @if(!$pilih_aws)
                                    <td>{{ $row->aws->name ?? '-' }}</td>
                                    <td>{{ $row->aws->location ?? '-' }}</td>
                                    @endif
                                    <td>{{ date('d-m-Y', strtotime($row->date)) }}</td>
                                    <td>{{ $row->min_temperature }}</td>
                                    <td>{{ $row->max_temperature }}</td>
                                    <td>{{ $row->avg_temperature }}</td>
                                    <td>{{ $row->min_humidity }}</td>
                                    <td>{{ $row->max_humidity }}</td>
                                    <td>{{ $row->avg_humidity }}</td>
                                    <td>{{ $row->min_pressure }}</td>
                                    <td>{{ $row->max_pressure }}</td>
                                    <td>{{ $row->avg_pressure }}</td>
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
                                    <td colspan="{{ $pilih_aws ? 18 : 20 }}" class="text-center text-muted">
                                        Tidak ada data dalam rentang tanggal ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $data->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </section>

    </main>

    @include('layouts.footer')
    @include('layouts.script')
</body>

</html>