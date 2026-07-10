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
                    <li class="breadcrumb-item"><a href="/evaluasi-kondisi">Evaluasi Kondisi</a></li>
                    <li class="breadcrumb-item active">Detail Anomali</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <h4 class="fw-bold mb-0 me-3">{{ $latest->aws->name }}</h4>
                        {{-- <span class="badge bg-danger-light text-danger border border-danger">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Status : {{ $latest->status }}
                        </span> --}}
                    </div>
                    @php
                        $utc = \Carbon\Carbon::parse($latest->timestamp)->timezone('UTC');
                        $wib = $utc->copy()->timezone('Asia/Jakarta');
                    @endphp

                    <p class="text-muted small mb-0 mt-2">
                        Waktu Kejadian :
                        {{ $utc->locale('id')->translatedFormat('d F Y, H:i') }} UTC
                        /
                        {{ $wib->format('H:i') }} WIB
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase fw-bold small text-muted mb-3">Data Saat Kejadian</h6>
                            <table class="table table-borderless table-sm small mb-0">
                                <tr><td>Suhu Udara</td><td class="text-end fw-bold">{{ $latest->temperature }}°C</td></tr>
                                <tr><td>Kelembapan</td><td class="text-end fw-bold">{{ $latest->humidity }}%</td></tr>
                                <tr><td>Tekanan</td><td class="text-end fw-bold">{{ $latest->pressure }} mbar</td></tr>
                                <tr><td>Suhu Air</td><td class="text-end fw-bold">{{ $latest->watertemp }}°C</td></tr>
                                <tr><td>Level Air</td><td class="text-end fw-bold">{{ $latest->waterlevel }} mm</td></tr>
                                <tr><td>Radiasi Matahari</td><td class="text-end fw-bold">{{ $latest->solrad }} W/m²</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase fw-bold small text-muted mb-3">Analisa dan Rekomendasi Tindakan</h6>
                            <p class="mb-0 lh-base">
                                Sistem mendeteksi adanya indikasi anomali berdasarkan hasil evaluasi menggunakan model Isolation Forest. Hal ini menunjukkan bahwa kombinasi nilai parameter sensor pada waktu tersebut berbeda dari pola normal yang telah dipelajari oleh model. Disarankan untuk melakukan pemeriksaan lebih lanjut terhadap kondisi sensor AWS serta membandingkan hasil pengamatan dengan pengukuran manual.
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
                            <h5 class="fw-bold">Suhu Air (°C)</h5>
                            <canvas id="chartSuhuAir" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold">Level Air (mm)</h5>
                            <canvas id="chartLevelAir" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // =========================
        // DATA DARI LARAVEL
        // =========================
        const labels = @json(
            $history->pluck('timestamp')->map(function($t){
                return \Carbon\Carbon::parse($t)->format('H:i');
            })
        );

        const suhu = @json($history->pluck('temperature'));
        const lembap = @json($history->pluck('humidity'));
        const tekanan = @json($history->pluck('pressure'));
        const radiasi = @json($history->pluck('solrad'));
        const suhuAir = @json($history->pluck('watertemp'));
        const levelAir = @json($history->pluck('waterlevel'));
        
        const blinkingPoint = {
            id: 'blinkingPoint',
            afterDatasetsDraw(chart) {
                const ctx = chart.ctx;
                const meta = chart.getDatasetMeta(0);

                if (!meta.data.length) return;

                // Ambil titik terakhir (anomali)
                const point = meta.data[meta.data.length - 1];

                // Radius berkedip
                const radius = 6 + Math.abs(Math.sin(Date.now() / 300)) * 6;

                ctx.save();

                ctx.beginPath();
                ctx.arc(point.x, point.y, radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(255, 0, 0, 0.25)';
                ctx.fill();

                ctx.restore();
            }
        };

        // =========================
        // FUNCTION CHART
        // =========================
        function createChart(id, label, data, color) {
            const chart = new Chart(document.getElementById(id), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: color,
                        tension: 0.3,
                        pointRadius: 5,
                        pointBackgroundColor: data.map((val, i) => {
                            return i === data.length - 1 ? 'red' : color;
                        })
                    }]
                },
                options: {
                    responsive: true,
                    animation: false, // agar lebih halus
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Waktu',
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                },
                plugins: [blinkingPoint]
            });

            // refresh chart agar efek berkedip berjalan
            setInterval(() => {
                chart.draw();
            }, 100);
        }

        // =========================
        // INIT CHART
        // =========================
        createChart('chartSuhu', 'Suhu', suhu, '#0d6efd');
        createChart('chartLembap', 'Lembap', lembap, '#0d6efd');
        createChart('chartTekanan', 'Tekanan', tekanan, '#0d6efd');
        createChart('chartRadiasi', 'Radiasi', radiasi, '#0d6efd');
        createChart('chartSuhuAir', 'Suhu Air', suhuAir, '#0d6efd');
        createChart('chartLevelAir', 'Level Air', levelAir, '#0d6efd');
    </script>

    @include('layouts.footer')

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    @include('layouts.script')

</body>

</html>