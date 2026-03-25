<!DOCTYPE html>
<html lang="en">

@include('layouts.header')

<head>
    <style>
        /* Agar hanya main yang scrollable */
        body {
            overflow: hidden; /* Mencegah scroll pada body utama */
        }
        #main {
            height: 100vh;
            overflow-y: auto; /* Mengaktifkan scroll hanya di area main */
            padding: 20px 30px;
        }
        .bg-danger-light { background-color: #fcebed; }
        .card { border-radius: 10px; }
        .breadcrumb-item + .breadcrumb-item::before { content: "/"; }
    </style>
</head>


<body>
    @include('layouts.loading')

    @include('layouts.navbar')

    @include('layouts.sidebar')

    <main id="main" class="main">

        <div class="pagetitle mb-4">
            <h1>Detail Anomali</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item">Evaluasi Kondisi</li>
                    <li class="breadcrumb-item active">Detail Anomali</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <h4 class="fw-bold mb-0 me-3">AWS Digi Banyuwangi</h4>
                        <span class="badge bg-danger-light text-danger border border-danger">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Status : ANOMALI
                        </span>
                    </div>
                    <p class="text-muted small mb-0 mt-2">Waktu Kejadian : 28 Agustus 2026, 05:00 WIB</p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase fw-bold small text-muted mb-3">Data Saat Kejadian</h6>
                            <table class="table table-borderless table-sm small mb-0">
                                <tr><td>Suhu Udara</td><td class="text-end fw-bold">40°C</td></tr>
                                <tr><td>Kelembapan</td><td class="text-end fw-bold">90%</td></tr>
                                <tr><td>Tekanan</td><td class="text-end fw-bold">1003 mbar</td></tr>
                                <tr><td>Suhu Panci</td><td class="text-end fw-bold">26°C</td></tr>
                                <tr><td>Level Panci</td><td class="text-end fw-bold">55 mm</td></tr>
                                <tr><td>Radiasi Matahari</td><td class="text-end fw-bold">187 W/m2</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase fw-bold small text-muted mb-3">Analisa dan Rekomendasi Tindakan</h6>
                            <p class="mb-0 lh-base">
                                Sistem mendeteksi anomali dengan skor -0.60 berdasarkan model <strong>Isolation Forest</strong>. Kombinasi nilai parameter sensor pada waktu kejadian teridentifikasi berada di luar pola normal. Disarankan untuk melakukan pemeriksaan lebih lanjut terhadap kondisi sensor AWS serta membandingkan hasil pengamatan dengan pengukuran manual.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold">Suhu Udara (°C)</h5>
                            <canvas id="chartSuhu" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold">Kelembapan (%)</h5>
                            <canvas id="chartLembap" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold">Tekanan Udara (mbar)</h5>
                            <canvas id="chartTekanan" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold">Radiasi Matahari (W/m²)</h5>
                            <canvas id="chartRadiasi" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold">Suhu Panci (°C)</h5>
                            <canvas id="chartSuhuPanci" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold">Level Panci (mm)</h5>
                            <canvas id="chartLevelPanci" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = ['00:00', '01:00', '02:00', '03:00', '04:00', '05:00'];

        function createChart(id, label, data, color) {
            new Chart(document.getElementById(id), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: color,
                        backgroundColor: color,
                        tension: 0.1,
                        pointRadius: 4,
                        pointBackgroundColor: color
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: false },
                        x: { title: { display: true, text: 'Waktu', font: { size: 10 } } }
                    }
                }
            });
        }

        // Inisialisasi Chart dengan Data Dummy
        createChart('chartSuhu', 'Suhu', [24, 26, 26, 27, 28, 40], '#0d6efd');
        createChart('chartLembap', 'Lembap', [73, 78, 82, 82, 86, 90], '#0d6efd');
        createChart('chartTekanan', 'Tekanan', [1000, 1001, 1002, 1003, 1003.5, 1004], '#0d6efd');
        createChart('chartRadiasi', 'Radiasi', [100, 200, 230, 235, 220, 280], '#0d6efd');
        createChart('chartSuhuPanci', 'Suhu Panci', [21, 23, 23, 24, 25, 26], '#0d6efd');
        createChart('chartLevelPanci', 'Level Panci', [52, 54, 55, 57, 58, 58], '#0d6efd');
    </script>

    @include('layouts.footer')

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    @include('layouts.script')

</body>

</html>