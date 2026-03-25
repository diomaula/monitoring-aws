<!DOCTYPE html>
<html lang="en">

@include('layouts.header')

<head>
    <style>
        .bg-danger-light { background-color: #fcebed; }
        .bg-success-light { background-color: #e8f5e9; }
        .card-title { font-size: 1.1rem; line-height: 1.3; }
        .badge { padding: 0.5em 0.8em; font-weight: 600; font-size: 0.75rem; }
        .breadcrumb-item + .breadcrumb-item::before { content: "/"; }
    </style>
</head>

<body>
    @include('layouts.loading')

    @include('layouts.navbar')

    @include('layouts.sidebar')

    <main id="main" class="main">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="pagetitle mb-0">
                <h1>Evaluasi Kondisi</h1>
                <nav>
                    <ol class="breadcrumb mb-0 mt-1">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Evaluasi Kondisi</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <button type="button" class="btn btn-outline-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#contaminationModal">
                <i class="bi bi-sliders me-1"></i> Pengaturan Sensitivitas Anomali
            </button>
        </div>

        <div class="modal fade" id="contaminationModal" tabindex="-1" aria-labelledby="contaminationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    
                    <!-- Header -->
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-dark" id="contaminationModalLabel">
                            <i class="bi bi-sliders text-primary me-2"></i>Pengaturan Sensitivitas Anomali
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Form -->
                    <form action="#" method="POST">
                        @csrf

                        <!-- Body -->
                        <div class="modal-body p-4">
                            
                            <!-- Deskripsi -->
                            <p class="text-muted small mb-4">
                                Atur tingkat sensitivitas dalam mendeteksi anomali pada data. Semakin tinggi nilai yang dipilih, semakin banyak data yang akan dikategorikan sebagai anomali.
                            </p>

                            <!-- Slider -->
                            <div class="mb-3 px-2">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label for="contaminationSlider" class="form-label fw-bold mb-0">
                                        Tingkat Sensitivitas
                                    </label>
                                    <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill" id="sliderValue">1%</span>
                                </div>

                                <input 
                                    type="range" 
                                    class="form-range" 
                                    min="1" 
                                    max="10" 
                                    step="1" 
                                    id="contaminationSlider" 
                                    name="contamination_value" 
                                    value="1"
                                >

                                <div class="d-flex justify-content-between text-muted small mt-2">
                                    <span class="fw-semibold">Rendah</span>
                                    <span class="fw-semibold">Tinggi</span>
                                </div>

                                <!-- Penjelasan tambahan -->
                                <small class="text-muted d-block mt-3">
                                    Nilai ini menentukan persentase data dengan skor anomali tertinggi yang akan diklasifikasikan sebagai anomali.
                                </small>
                            </div>

                            <!-- Info -->
                            {{-- <div class="alert alert-info border-0 small mt-4 mb-0 d-flex align-items-center">
                                <i class="bi bi-info-circle-fill fs-5 me-2 text-info"></i> 
                                <div>
                                    Nilai yang disarankan untuk operasional adalah sekitar <strong>5%</strong> agar deteksi tetap seimbang.
                                </div>
                            </div> --}}

                        </div>

                        <!-- Footer -->
                        <div class="modal-footer bg-light border-top-0">
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                Terapkan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="card shadow-sm border-0 mb-4" style="border-left: 5px solid #dc3545 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold m-0 p-0 text-dark">AWS Digi <br>Banyuwangi</h5>
                                <span class="badge bg-danger-light text-danger border border-danger"><i class="bi bi-exclamation-triangle-fill"></i> ANOMALI</span>
                            </div>
                            <div class="mb-3">
                                <label class="text-uppercase small fw-bold text-muted d-block">Kondisi Data :</label>
                                <span class="small fw-semibold">Terdeteksi Menyimpang dari Pola Normal</span>
                            </div>
                            <div class="mb-4">
                                <label class="text-uppercase small fw-bold text-muted d-block">Score :</label>
                                <div class="progress mt-1" style="height: 8px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 80%"></div>
                                </div>
                                <span class="small text-danger fw-bold mt-1 d-block">-0.60 (Sangat Menyimpang)</span>
                            </div>
                            <div class="d-grid">
                                <a href="{{ route('detail-evaluasi-kondisi') }}" class="btn btn-primary fw-bold">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card shadow-sm border-0 mb-4" style="border-left: 5px solid #2cd485 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold m-0 p-0 text-dark">AWS Maritim <br>Gilimanuk</h5>
                                <span class="badge bg-success-light text-success border border-success"><i class="bi bi-check-circle-fill"></i> NORMAL</span>
                            </div>
                            <div class="mb-3">
                                <label class="text-uppercase small fw-bold text-muted d-block">Kondisi Data :</label>
                                <span class="small fw-semibold">Data Berada Dalam Pola Normal</span>
                            </div>
                            <div class="mb-4">
                                <label class="text-uppercase small fw-bold text-muted d-block">Score :</label>
                                <div class="progress mt-1" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 60%"></div>
                                </div>
                                <span class="small text-success fw-bold mt-1 d-block">0.12 (Normal)</span>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-light fw-bold text-muted" disabled>Tidak Ada</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card shadow-sm border-0 mb-4" style="border-left: 5px solid #dc3545 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold m-0 p-0 text-dark">AWS Maritim <br>Ketapang</h5>
                                <span class="badge bg-danger-light text-danger border border-danger"><i class="bi bi-exclamation-triangle-fill"></i> ANOMALI</span>
                            </div>
                            <div class="mb-3">
                                <label class="text-uppercase small fw-bold text-muted d-block">Kondisi Data :</label>
                                <span class="small fw-semibold">Terdeteksi Menyimpang dari Pola Normal</span>
                            </div>
                            <div class="mb-4">
                                <label class="text-uppercase small fw-bold text-muted d-block">Score :</label>
                                <div class="progress mt-1" style="height: 8px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 70%"></div>
                                </div>
                                <span class="small text-danger fw-bold mt-1 d-block">-0.45 (Menyimpang)</span>
                            </div>
                            <div class="d-grid">
                                <a href="#" class="btn btn-primary fw-bold">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-2">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Riwayat Data Anomali</h5>
                    
                    <form class="row g-3 align-items-end mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Bulan</label>
                            <select class="form-select">
                                <option selected>Agustus</option>
                                <option>Juli</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Tahun</label>
                            <select class="form-select">
                                <option selected>2026</option>
                                <option>2025</option>
                            </select>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-primary me-2"><i class="bi bi-search me-1"></i> Tampilkan</button>
                            <button type="button" class="btn btn-danger"><i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>AWS Digi Banyuwangi</td>
                                    <td>28-08-2026</td>
                                    <td><span class="badge bg-danger-light text-danger border border-danger">ANOMALI</span></td>
                                    <td class="text-danger fw-bold">-0.60</td>
                                    <td class="text-center"><a href="#" class="text-dark"><i class="bi bi-eye"></i></a></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>AWS Maritim Ketapang</td>
                                    <td>26-08-2026</td>
                                    <td><span class="badge bg-danger-light text-danger border border-danger">ANOMALI</span></td>
                                    <td class="text-danger fw-bold">-0.45</td>
                                    <td class="text-center"><a href="#" class="text-dark"><i class="bi bi-eye"></i></a></td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td>AWS Maritim Ketapang</td>
                                    <td>25-08-2026</td>
                                    <td><span class="badge bg-danger-light text-danger border border-danger">ANOMALI</span></td>
                                    <td class="text-danger fw-bold">-0.48</td>
                                    <td class="text-center"><a href="#" class="text-dark"><i class="bi bi-eye"></i></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </main>@include('layouts.footer')

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    @include('layouts.script')

    <script>
        const slider = document.getElementById('contaminationSlider');
        const sliderValue = document.getElementById('sliderValue');

        slider.addEventListener('input', function () {
            sliderValue.innerText = this.value + '%';
        });
    </script>

</body>

</html>