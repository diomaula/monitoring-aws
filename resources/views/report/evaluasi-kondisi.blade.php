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
        <div class="pagetitle mb-4">
            <h1>Evaluasi Kondisi</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Evaluasi Kondisi</li>
                </ol>
            </nav>
        </div><section class="section">
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
                    <div class="card shadow-sm border-0 mb-4" style="border-left: 5px solid #198754 !important;">
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


    </main><!-- End #main -->

    @include('layouts.footer')

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    @include('layouts.script')

</body>

</html>