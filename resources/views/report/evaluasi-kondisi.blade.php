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
        
        <!-- BUTTON -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <button type="button" 
                class="btn btn-outline-primary fw-bold shadow-sm" 
                data-bs-toggle="modal" 
                data-bs-target="#contaminationModal">
                <i class="bi bi-sliders me-1"></i> Pengaturan Sensitivitas Anomali
            </button>
            <p class="small text-muted">
                Threshold saat ini: <strong>{{ number_format($contamination, 2) }}</strong>
            </p>
        </div>

        <!-- MODAL -->
        <div class="modal fade" id="contaminationModal" tabindex="-1" aria-labelledby="contaminationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    
                    <!-- HEADER -->
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-dark" id="contaminationModalLabel">
                            <i class="bi bi-sliders text-primary me-2"></i>
                            Pengaturan Sensitivitas Anomali
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- FORM -->
                    <form action="{{ route('evaluasi-kondisi') }}" method="GET">
                        
                        <!-- BODY -->
                        <div class="modal-body p-4">
                            
                            <p class="text-muted small mb-4">
                                Atur tingkat sensitivitas dalam mendeteksi anomali pada data.
                                Semakin tinggi nilai, semakin banyak data dianggap anomali.
                            </p>

                            <!-- SLIDER -->
                            <div class="mb-3 px-2">
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label fw-bold mb-0">
                                        Tingkat Sensitivitas
                                    </label>

                                    <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill" id="sliderValue">
                                        {{ ($contamination ?? 0.01) * 100 }}%
                                    </span>
                                </div>

                                <input 
                                    type="range"
                                    class="form-range"
                                    min="1"
                                    max="10"
                                    step="1"
                                    id="contaminationSlider"
                                    name="contamination_value"
                                    value="{{ ($contamination ?? 0.01) * 100 }}"
                                >

                                <div class="d-flex justify-content-between text-muted small mt-2">
                                    <span class="fw-semibold">Rendah</span>
                                    <span class="fw-semibold">Tinggi</span>
                                </div>

                                <small class="text-muted d-block mt-3">
                                    Persentase data dengan skor terendah yang akan dianggap sebagai anomali.
                                </small>
                            </div>

                        </div>

                        <!-- FOOTER -->
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
                @forelse($result as $aws)
                    @php
                        $isAnomali = $aws['status'] == 'ANOMALI';

                        $borderColor = $isAnomali ? '#dc3545' : '#198754';
                        $badgeClass = $isAnomali 
                            ? 'bg-danger-light text-danger border border-danger' 
                            : 'bg-success-light text-success border border-success';

                        $icon = $isAnomali 
                            ? 'bi-exclamation-triangle-fill' 
                            : 'bi-check-circle-fill';

                        $progressColor = $isAnomali ? 'bg-danger' : 'bg-success';
                        $textColor = $isAnomali ? 'text-danger' : 'text-success';

                        // mapping score ke persen (biar visual enak)
                        $progress = min(max(abs($aws['score']) * 100, 10), 100);
                    @endphp

                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-sm border-0 mb-4" 
                            style="border-left: 5px solid {{ $borderColor }} !important;">

                            <div class="card-body p-4">
                                
                                <!-- HEADER -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title fw-bold m-0 p-0 text-dark">
                                        {{ $aws['nama'] }}
                                    </h5>

                                    <span class="badge {{ $badgeClass }}">
                                        <i class="bi {{ $icon }}"></i> {{ $aws['status'] }}
                                    </span>
                                </div>

                                <!-- KONDISI -->
                                <div class="mb-3">
                                    <label class="text-uppercase small fw-bold text-muted d-block">Kondisi Data :</label>
                                    <span class="small fw-semibold">
                                        {{ $isAnomali 
                                            ? 'Terdeteksi Menyimpang dari Pola Normal' 
                                            : 'Data Berada Dalam Pola Normal' }}
                                    </span>
                                </div>

                                <!-- SCORE -->
                                <div class="mb-4">
                                    <label class="text-uppercase small fw-bold text-muted d-block">Score :</label>

                                    <div class="progress mt-1" style="height: 8px;">
                                        <div class="progress-bar {{ $progressColor }}" 
                                            style="width: {{ $progress }}%">
                                        </div>
                                    </div>

                                    <span class="small fw-bold mt-1 d-block {{ $textColor }}">
                                        {{ number_format($aws['score'], 2) }}
                                    </span>
                                </div>

                                <!-- BUTTON -->
                                <div class="d-grid">
                                    @if($isAnomali)
                                        <a href="{{ route('detail-evaluasi-kondisi', $aws['aws_id']) }}" class="btn btn-primary fw-bold">
                                            Lihat Detail
                                        </a>
                                    @else
                                        <button class="btn btn-light fw-bold text-muted" disabled>
                                            Tidak Ada
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            Data belum tersedia
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="card shadow-sm border-0 mt-2">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Riwayat Data Anomali</h5>
                    
                    <!-- FILTER -->
                    <form method="GET" action="{{ route('evaluasi-kondisi') }}" class="row g-3 align-items-end mb-4">
                        
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Bulan</label>
                            <select name="bulan" class="form-select">
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Tahun</label>
                            <select name="tahun" class="form-select">
                                @foreach(range(now()->year, now()->year - 5) as $y)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- PENTING: bawa contamination juga -->
                        <input type="hidden" name="contamination_value" value="{{ $contamination * 100 }}">

                        <div class="col-md-6 text-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search me-1"></i> Tampilkan
                            </button>
                        </div>
                    </form>

                    <!-- TABLE -->
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

                                @forelse($riwayat as $i => $item)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>

                                    <td>{{ $item['nama'] }}</td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y H:i') }}
                                    </td>

                                    <td>
                                        <span class="badge 
                                            {{ $item['status']=='ANOMALI' 
                                                ? 'bg-danger-light text-danger border border-danger' 
                                                : 'bg-success-light text-success border border-success' }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>

                                    <td class="{{ $item['status']=='ANOMALI' ? 'text-danger' : 'text-success' }} fw-bold">
                                        {{ number_format($item['score'], 2) }}
                                    </td>

                                    <td class="text-center">
                                        @if($item['status']=='ANOMALI')
                                            <a href="{{ route('detail-evaluasi-kondisi', $item['id']) }}" class="text-dark">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        Tidak ada data
                                    </td>
                                </tr>
                                @endforelse

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

        // update saat digeser
        slider.addEventListener('input', function () {
            sliderValue.innerText = this.value + '%';
        });

        // set awal (biar tidak kosong)
        window.addEventListener('DOMContentLoaded', function () {
            sliderValue.innerText = slider.value + '%';
        });
    </script>

</body>

</html>