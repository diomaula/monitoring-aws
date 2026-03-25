@extends('layouts.app')

@section('title', 'Laporan Deteksi Anomali')

@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Evaluasi Kondisi</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item">Report</li>
          <li class="breadcrumb-item active">Anomali</li>
        </ol>
      </nav>
    </div><section class="section dashboard">
      <div class="row">

        <div class="col-xxl-4 col-md-6">
          <div class="card info-card sales-card">
            <div class="card-body">
              <h5 class="card-title">Total Anomali <span>| Bulan Ini</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-danger-light text-danger">
                  <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="ps-3">
                  <h6>{{ $anomalies->count() }}</h6>
                  <span class="text-muted small pt-2 ps-1">Kejadian terdeteksi</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xxl-4 col-md-6">
          <div class="card info-card revenue-card">
            <div class="card-body">
              <h5 class="card-title">Status Terkini <span>| Realtime</span></h5>
              <div class="d-flex align-items-center">
                {{-- Logika warna ikon berdasarkan status terakhir --}}
                @php
                    $lastStatus = $anomalies->first()->status_anomali ?? 'Normal';
                    $isDanger = $lastStatus == 'ANOMALI';
                @endphp
                
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center {{ $isDanger ? 'bg-danger-light text-danger' : 'bg-success-light text-success' }}">
                  <i class="bi {{ $isDanger ? 'bi-x-octagon' : 'bi-check-circle' }}"></i>
                </div>
                <div class="ps-3">
                  <h6>{{ $isDanger ? 'PERLU PERHATIAN' : 'AMAN' }}</h6>
                  <span class="text-muted small pt-2 ps-1">Update terakhir: Hari ini</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xxl-4 col-xl-12">
          <div class="card info-card customers-card">
            <div class="card-body">
              <h5 class="card-title">Confidence Score <span>| Rata-rata</span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-cpu"></i>
                </div>
                <div class="ps-3">
                  {{-- Contoh rata-rata score, sesuaikan dengan data controller --}}
                  <h6>94.2%</h6>
                  <span class="text-success small pt-1 fw-bold">High</span> <span class="text-muted small pt-2 ps-1">Confidence</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card">
            <div class="filter">
              <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start"><h6>Filter</h6></li>
                <li><a class="dropdown-item" href="#">Hari Ini</a></li>
                <li><a class="dropdown-item" href="#">Bulan Ini</a></li>
              </ul>
            </div>

            <div class="card-body">
              <h5 class="card-title">Tren Suhu vs Prediksi AI <span>/ Bulan Ini</span></h5>
              
              <div id="reportsChart"></div>
              
              <div class="mt-3">
                <div class="d-flex align-items-center small">
                    <i class="bi bi-circle-fill text-primary me-2"></i> Data Normal
                    <i class="bi bi-x-lg text-danger ms-3 me-2"></i> Anomali Terdeteksi
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card recent-sales overflow-auto">
            <div class="card-body">
              <h5 class="card-title">Riwayat Anomali <span>| Terdeteksi Baru-baru Ini</span></h5>

              <table class="table table-borderless datatable">
                <thead>
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Waktu Kejadian</th>
                    <th scope="col">Suhu (°C)</th>
                    <th scope="col">Kelembaban (%)</th>
                    <th scope="col">AI Score</th>
                    <th scope="col">Tindakan</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($anomalies as $data)
                  <tr>
                    <th scope="row"><a href="#">#{{ $data->id }}</a></th>
                    <td>{{ $data->created_at->format('d M Y, H:i') }}</td>
                    <td class="text-danger fw-bold">{{ $data->suhu }}°C</td>
                    <td>{{ $data->kelembaban }}%</td>
                    <td>
                        <span class="badge bg-info text-dark">
                            <i class="bi bi-stars me-1"></i> {{ number_format($data->ai_score, 3) }}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#detailModal{{ $data->id }}">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                    </td>
                  </tr>

                  <div class="modal fade" id="detailModal{{ $data->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header bg-light">
                          <h5 class="modal-title text-danger">
                              <i class="bi bi-exclamation-triangle-fill me-2"></i>Detail Anomali #{{ $data->id }}
                          </h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <div>
                                    Pola suhu tidak wajar terdeteksi pada <strong>{{ $data->created_at->format('H:i') }} WIB</strong>.
                                    Nilai menyimpang <strong>+{{ number_format($data->suhu - 24, 1) }}°C</strong> dari rata-rata jam tersebut.
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="p-3 border rounded bg-light text-center">
                                        <small class="text-muted d-block">Suhu Terukur</small>
                                        <span class="fs-4 fw-bold text-danger">{{ $data->suhu }}°C</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded bg-light text-center">
                                        <small class="text-muted d-block">Normal (Prediksi)</small>
                                        <span class="fs-4 fw-bold text-success">~24.5°C</span>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold mt-4">Rekomendasi Tindakan (SOP)</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-start">
                                    <input class="form-check-input me-2" type="checkbox" value="" id="check1{{ $data->id }}">
                                    <label class="form-check-label stretched-link" for="check1{{ $data->id }}">
                                        Periksa apakah pintu sangkar terbuka.
                                    </label>
                                </li>
                                <li class="list-group-item d-flex align-items-start">
                                    <input class="form-check-input me-2" type="checkbox" value="" id="check2{{ $data->id }}">
                                    <label class="form-check-label stretched-link" for="check2{{ $data->id }}">
                                        Cek kondisi fisik sensor (berdebu/tertutup).
                                    </label>
                                </li>
                                <li class="list-group-item d-flex align-items-start">
                                    <input class="form-check-input me-2" type="checkbox" value="" id="check3{{ $data->id }}">
                                    <label class="form-check-label stretched-link" for="check3{{ $data->id }}">
                                        Bandingkan dengan termometer manual.
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                          <button type="button" class="btn btn-danger">Buat Tiket Maintenance</button>
                        </div>
                      </div>
                    </div>
                  </div>@endforeach
                </tbody>
              </table>

            </div>
          </div>
        </div>

      </div>
    </section>

</main><script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Data dari Controller (Variable PHP ke JS)
    const categories = @json($categories); // Array tanggal/jam
    const dataSuhu = @json($suhuData); // Array nilai suhu
    const anomaliPoints = @json($anomaliPoints); // Array titik anomali {x, y}

    // Ubah format titik anomali agar sesuai format ApexCharts Annotations
    const annotations = anomaliPoints.map(point => {
        return {
            x: point.x,
            y: point.y,
            marker: {
                size: 6,
                fillColor: '#fff',
                strokeColor: '#dc3545',
                radius: 2,
                cssClass: 'apexcharts-custom-class'
            },
            label: {
                borderColor: '#dc3545',
                style: {
                    color: '#fff',
                    background: '#dc3545',
                    fontSize: '10px',
                    padding: { left: 5, right: 5, top: 2, bottom: 2}
                },
                text: 'ANOMALI',
            }
        };
    });

    new ApexCharts(document.querySelector("#reportsChart"), {
        series: [{
            name: 'Suhu (°C)',
            data: dataSuhu,
        }],
        chart: {
            height: 350,
            type: 'area', // Area chart terlihat lebih modern seperti di index.html
            toolbar: { show: false },
            fontFamily: 'Nunito, sans-serif' // Font bawaan NiceAdmin
        },
        markers: {
            size: 4 // Ukuran titik data normal
        },
        colors: ['#4154f1'], // Warna biru NiceAdmin
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.3,
                opacityTo: 0.4,
                stops: [0, 90, 100]
            }
        },
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: categories, // Label sumbu X
            tooltip: { enabled: false }
        },
        yaxis: {
            title: { text: 'Suhu (°C)' }
        },
        // Fitur Utama: Menandai titik Anomali
        annotations: {
            points: annotations
        },
        tooltip: {
            theme: 'light',
            x: { format: 'dd MMM HH:mm' },
        }
    }).render();
});
</script>

@endsection