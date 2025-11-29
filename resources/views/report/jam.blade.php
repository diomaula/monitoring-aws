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
            <h1>Laporan Data Mentah AWS
                @if($pilih_aws)
                - {{ $aws_list->firstWhere('id', $pilih_aws)->name ?? '' }}
                @endif
            </h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item active">Laporan Data Mentah AWS</li>
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

                    <form method="GET" class="row g-3 mb-4" style="margin-bottom: 40px; margin-top: 20px;">
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
                            <select id="aws_id" name="aws_id" class="form-control">
                                <option value="">Semua AWS</option>
                                @foreach($aws_list as $aws)
                                <option value="{{ $aws->id }}" @if($pilih_aws==$aws->id) selected @endif>
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
                            <a href="{{ route('laporan.jamExcel', request()->all()) }}" class="btn btn-success w-100">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                        </div>
                    </form>

                    {{-- Tabel data mentah --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover text-center align-middle">
                            <thead class="table-primary">
                                <tr>
                                    @if(!$pilih_aws)
                                    <th>AWS</th>
                                    <th>Lokasi</th>
                                    @endif
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Temperature (°C)</th>
                                    <th>Humidity (%)</th>
                                    <th>Pressure (hPa)</th>
                                    <th>Rainfall (mm)</th>
                                    <th>Wind Speed (m/s)</th>
                                    <th>Wind Direction</th>
                                    <th>Panci Temp</th>
                                    <th>Panci Level</th>
                                    <th>Solar Radiation</th>
                                    <th>Water Temp</th>
                                    <th>Water Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($laporan as $row)
                                <tr>
                                    @if(!$pilih_aws)
                                    <td>{{ $row->aws_name }}</td>
                                    <td>{{ $row->location }}</td>
                                    @endif
                                    <td>{{ date('d-m-Y', strtotime($row->timestamp)) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->timestamp)->format('H:i') }}</td>
                                    <td>{{ $row->temperature ?? '-' }}</td>
                                    <td>{{ $row->humidity ?? '-' }}</td>
                                    <td>{{ $row->pressure ?? '-' }}</td>
                                    <td>{{ $row->rainfall ?? '-' }}</td>
                                    <td>{{ $row->wind_speed ?? '-' }}</td>
                                    <td>
                                        @if($row->wind_direction!==null)
                                        @php
                                        $dirs=['N','NE','E','SE','S','SW','W','NW'];
                                        $idx=round($row->wind_direction/45)%8;
                                        @endphp
                                        {{ $dirs[$idx] }} ({{ $row->wind_direction }}°)
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>{{ $row->pancitemp ?? '-' }}</td>
                                    <td>{{ $row->pancilevel ?? '-' }}</td>
                                    <td>{{ $row->solrad ?? '-' }}</td>
                                    <td>{{ $row->watertemp ?? '-' }}</td>
                                    <td>{{ $row->waterlevel ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $pilih_aws ? 13 : 15 }}" class="text-center text-muted">Tidak ada data.</td>
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