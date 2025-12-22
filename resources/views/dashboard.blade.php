<!DOCTYPE html>
<html lang="en">

  @include('layouts.header')

  <body>
    @include('layouts.loading')
    @include('layouts.navbar')
    @include('layouts.sidebar')

    <main id="main" class="main">

      <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <section class="section dashboard">
        <div class="row">
          <div class="col-lg-12">
            <div class="row">

              <div class="col-xxl-3 col-md-6"> <div class="card info-card sales-card">
                  <div class="card-body">
                    <h5 class="card-title">Total AWS</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-hdd-network"></i>
                      </div>
                      <div class="ps-3">
                        <h6 id="total-aws"><div class="spinner-border spinner-border-sm" role="status"></div></h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xxl-3 col-md-6">
                <div class="card info-card revenue-card">
                  <div class="card-body">
                    <h5 class="card-title">AWS Hijau</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-check-circle"></i>
                      </div>
                      <div class="ps-3">
                        <h6 id="aws-hijau"><div class="spinner-border spinner-border-sm" role="status"></div></h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xxl-3 col-md-6">
                <div class="card info-card customers-card">
                  <div class="card-body">
                    <h5 class="card-title">AWS Merah</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-exclamation-triangle"></i>
                      </div>
                      <div class="ps-3">
                        <h6 id="aws-merah"><div class="spinner-border spinner-border-sm" role="status"></div></h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xxl-3 col-md-6">
                <div class="card info-card sales-card border-warning"> <div class="card-body">
                    <h5 class="card-title">Prediksi Suhu <span>| 1 Jam</span></h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning text-white">
                        <i class="bi bi-magic"></i> </div>
                      <div class="ps-3">
                        <h6 id="ai-prediction-value">--.--°C</h6>
                        <span class="text-muted small pt-2 ps-1">AI Nowcasting</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12">
                  <div class="card">
                      <div class="card-body">
                          <h5 class="card-title">Grafik Monitoring & Prediksi AI</h5>
                          <div id="reportsChart"></div> 
                      </div>
                  </div>
              </div>

              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Peta AWS Banyuwangi</h5>
                    <div id="map" style="height: 500px;">Loading Map...</div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>

      <!-- Leaflet & ApexCharts -->
      <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
      <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


      <script>
        // 1. Konfigurasi Map (Tidak Berubah)
        var map = L.map('map').setView([-8.2191, 114.3693], 10);
        var street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}');
        var light = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 20
        });
        L.control.layers({
            "Street": street,
            "Satellite": satellite,
            "Light": light
        }).addTo(map);
        var markers = [];

        // 2. Fungsi Update Utama
        function updateDashboard() {
            
            // --- FETCH 1: Data untuk MAP & Card Status (Real-time) ---
            fetch('{{ url("/api/stations?region=banyuwangi") }}')
                .then(async res => {
                    if (!res.ok) throw new Error(`Map HTTP Error ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    // Update Cards Total/Hijau/Merah
                    document.getElementById('total-aws').innerText = data.length;
                    document.getElementById('aws-hijau').innerText = data.filter(d => d.status === 'HIJAU').length;
                    document.getElementById('aws-merah').innerText = data.filter(d => d.status === 'MERAH').length;

                    // Update Markers Peta
                    markers.forEach(m => map.removeLayer(m));
                    markers = [];
                    data.forEach(station => {
                        var iconUrl = station.status === 'MERAH' ?
                            'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png' :
                            'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png';
                        
                        var marker = L.marker([station.lat, station.lng], {
                            icon: L.icon({
                                iconUrl: iconUrl,
                                shadowUrl: 'https://unpkg.com/leaflet@1.9.3/dist/images/marker-shadow.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41],
                                popupAnchor: [1, -34]
                            })
                        }).addTo(map);
                        marker.bindPopup(`<b>${station.name}</b><br>ID: ${station.id}<br>Status: ${station.status}`);
                        markers.push(marker);
                    });

                    // --- FETCH 2: Data untuk CHART & AI (Database Lokal) ---
                    // Menggunakan Route Baru: /api/chart-ai-data
                    return fetch('{{ url("/api/chart-ai-data") }}');
                })
                .then(async res => {
                    if (!res.ok) throw new Error(`Chart HTTP Error ${res.status}`);
                    return res.json();
                })
                .then(response => {
                    console.log("Chart & AI Data:", response);

                    // Pisahkan data Real dan Prediksi
                    // API mengembalikan: { real: [...], prediction: [...] }
                    const realData = response.real || [];
                    const predData = response.prediction || [];

                    // Mapping Data Real
                    let categories = realData.map(d => d.date);
                    let rainfall = realData.map(d => parseFloat(d.rainfall));
                    let temperature = realData.map(d => parseFloat(d.temperature));
                    let humidity = realData.map(d => parseFloat(d.humidity));

                    // Siapkan Data Series untuk Prediksi
                    // Isi awal dengan null sebanyak data real agar posisi prediksi ada di ujung kanan
                    let predictionSeries = new Array(temperature.length).fill(null);

                    // Logika Menggabungkan Data Real dengan Prediksi
                    if (predData.length > 0 && temperature.length > 0) {
                        const lastRealTemp = temperature[temperature.length - 1]; // Titik terakhir data asli
                        const predValue = parseFloat(predData[0].nilai_prediksi);
                        const predTime = predData[0].date;

                        // 1. Update Teks di Card AI (Jika elemen ada)
                        const cardAi = document.getElementById('ai-prediction-value');
                        if (cardAi) cardAi.innerText = predValue.toFixed(1) + "°C";

                        // 2. Sambungkan garis: Titik terakhir data asli -> Titik prediksi
                        predictionSeries[temperature.length - 1] = lastRealTemp; // Titik sambung
                        
                        // 3. Tambahkan titik masa depan
                        categories.push(predTime); // Tambah jam prediksi ke sumbu X
                        predictionSeries.push(predValue); // Tambah nilai prediksi
                        
                        // 4. Isi series lain dengan null agar panjang array sama
                        rainfall.push(null);
                        temperature.push(null);
                        humidity.push(null);
                    }

                    // Render Chart
                    // Bersihkan chart lama sebelum render ulang
                    document.querySelector("#reportsChart").innerHTML = "";

                    var options = {
                        series: [
                            { name: 'Curah Hujan (mm)', type: 'column', data: rainfall },
                            { name: 'Suhu Aktual (°C)', type: 'line', data: temperature },
                            { name: 'Prediksi AI (°C)', type: 'line', data: predictionSeries }, // Series Baru
                            { name: 'Kelembapan (%)', type: 'line', data: humidity }
                        ],
                        chart: { 
                            height: 350, 
                            type: 'line', 
                            toolbar: { show: false },
                            animations: { enabled: false } // Matikan animasi agar tidak kedip saat refresh
                        },
                        stroke: { 
                            width: [0, 3, 3, 3], 
                            curve: 'smooth',
                            dashArray: [0, 0, 5, 0] // 5 = Garis Putus-putus untuk Prediksi AI
                        },
                        dataLabels: { 
                            enabled: true, 
                            enabledOnSeries: [1, 2] // Tampilkan angka di Suhu & Prediksi
                        },
                        labels: categories,
                        xaxis: { type: 'category' },
                        yaxis: [
                            { title: { text: "Curah Hujan (mm)" } },
                            { opposite: true, title: { text: "Suhu & Kelembapan" } }
                        ],
                        // Urutan Warna: Hujan(Merah), Suhu(Hijau), Prediksi(Kuning/Oranye), Lembab(Biru)
                        colors: ['#FF0000', '#2eca6a', '#ffc107', '#4154f1'], 
                        tooltip: { shared: true, intersect: false }
                    };

                    var chart = new ApexCharts(document.querySelector("#reportsChart"), options);
                    chart.render();
                })
                .catch(err => {
                    console.error("Gagal update dashboard:", err.message);
                    // Opsional: Tampilkan indikator error di UI
                });
        }

        // Jalankan pertama kali
        updateDashboard();
        
        // Jalankan otomatis setiap 1 menit
        setInterval(updateDashboard, 60000); 
    </script>

    </main><!-- End #main -->

    @include('layouts.footer')

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    @include('layouts.script')

  </body>

</html>