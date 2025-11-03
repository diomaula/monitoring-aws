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

              <!-- Card Total AWS -->
              <div class="col-xxl-4 col-md-6">
                <div class="card info-card sales-card">
                  <div class="card-body">
                    <h5 class="card-title">Total AWS</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-hdd-network"></i>
                      </div>
                      <div class="ps-3">
                        <h6 id="total-aws"><div class="inline-loader"></h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- AWS Hijau -->
              <div class="col-xxl-4 col-md-6">
                <div class="card info-card revenue-card">
                  <div class="card-body">
                    <h5 class="card-title">AWS Hijau</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-check-circle"></i>
                      </div>
                      <div class="ps-3">
                        <h6 id="aws-hijau"><div class="inline-loader"></h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- AWS Merah -->
              <div class="col-xxl-4 col-xl-12">
                <div class="card info-card customers-card">
                  <div class="card-body">
                    <h5 class="card-title">AWS Merah</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-exclamation-triangle"></i>
                      </div>
                      <div class="ps-3">
                        <h6 id="aws-merah"><div class="inline-loader"></h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Chart -->
              {{-- <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <h5 class="card-title mb-0">Monitoring Curah Hujan, Suhu & Kelembapan AWS</h5>
                      <select id="chartFilter" class="form-select w-auto" onchange="updateDashboard()">
                        <option value="daily" selected>Harian (7 Hari)</option>
                        <option value="hourly">Per Jam (1 Hari)</option>
                      </select>
                    </div>
                    <div id="reportsChart">
                      <div class="inline-loader"></div>
                    </div>
                  </div>
                </div>
              </div> --}}


              <!-- Peta -->
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

        function updateDashboard() {
          fetch('{{ url("/api/stations?region=banyuwangi") }}')
              .then(async res => {
                  // Cek status HTTP
                  if (!res.ok) {
                      throw new Error(`HTTP Error ${res.status} - ${res.statusText}`);
                  }

                  // Ambil dulu sebagai text supaya bisa dilihat kalau gagal parse JSON
                  const text = await res.text();
                  try {
                      return JSON.parse(text);
                  } catch (e) {
                      console.error("Response bukan JSON. Ini isinya:", text);
                      throw new Error("Response bukan JSON (kemungkinan halaman error / redirect)");
                  }
              })

              .then(data => {
                  console.log("Data stations:", data);

                  // Update cards
                  document.getElementById('total-aws').innerText = data.length;
                  document.getElementById('aws-hijau').innerText = data.filter(d => d.status === 'HIJAU').length;
                  document.getElementById('aws-merah').innerText = data.filter(d => d.status === 'MERAH').length;

                  // Update markers
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

                  // Fetch chart data
                  return fetch('{{ url("/api/stations") }}');
              })
              
              .then(async res => {
                  if (!res.ok) throw new Error(`HTTP Error ${res.status} - ${res.statusText}`);
                  const text = await res.text();
                  try {
                      return JSON.parse(text);
                  } catch (e) {
                      console.error("Response bukan JSON. Ini isinya:", text);
                      throw new Error("Response bukan JSON (kemungkinan halaman error / redirect)");
                  }
              })

              .then(avgData => {
                  console.log("Data chart:", avgData);

                  if (!Array.isArray(avgData)) {
                      avgData = Object.values(avgData);
                  }
                  
                  let categories = avgData.map(d => d.date);
                  let rainfall = avgData.map(d => parseFloat(d.rainfall));
                  let temperature = avgData.map(d => parseFloat(d.temperature));
                  let humidity = avgData.map(d => parseFloat(d.humidity));

                  var chart = new ApexCharts(document.querySelector("#reportsChart"), {
                      series: [
                          { name: 'Curah Hujan (mm)', type: 'column', data: rainfall },
                          { name: 'Suhu (°C)', type: 'line', data: temperature },
                          { name: 'Kelembapan (%)', type: 'line', data: humidity }
                      ],
                      chart: { height: 350, type: 'line', toolbar: { show: false } },
                      stroke: { width: [0, 3, 3] },
                      dataLabels: { enabled: true, enabledOnSeries: [1, 2] },
                      labels: categories,
                      xaxis: { type: 'category' },
                      yaxis: [
                          { title: { text: "Curah Hujan (mm)" } },
                          { opposite: true, title: { text: "Suhu & Kelembapan" } }
                      ],
                      colors: ['#FF0000', '#2eca6a', '#4154f1'],
                      tooltip: { shared: true, intersect: false }
                  });
                  chart.render();
              })
              
              .catch(err => {
                  console.error("Gagal memuat data:", err.message);
                  document.getElementById('total-aws').innerText = "Error";
                  document.getElementById('aws-hijau').innerText = "Error";
                  document.getElementById('aws-merah').innerText = "Error";
              });
        }

        updateDashboard();
        setInterval(updateDashboard, 60000); 
      </script>

      {{-- Chart Dummy --}}
      {{-- <script>
        document.addEventListener("DOMContentLoaded", () => {
          // Data dummy 7 hari terakhir
          let categories = ["2025-07-22", "2025-07-23", "2025-07-24", "2025-07-25", "2025-07-26", "2025-07-27", "2025-07-28"];
          let rainfall = [10, 20, 0, 5, 15, 0, 30]; // Curah hujan mm
          let temperature = [27, 28, 29, 30, 28, 27, 26]; // Suhu °C
          let humidity = [80, 82, 78, 75, 77, 79, 81]; // Kelembapan %

          new ApexCharts(document.querySelector("#reportsChart"), {
            series: [{
                name: 'Curah Hujan (mm)',
                type: 'line',
                data: rainfall
              },
              {
                name: 'Suhu (°C)',
                type: 'line',
                data: temperature
              },
              {
                name: 'Kelembapan (%)',
                type: 'line',
                data: humidity
              }
            ],
            chart: {
              height: 350,
              type: 'line',
              toolbar: {
                show: false
              }
            },
            stroke: {
              curve: 'smooth',
              width: 3
            },
            dataLabels: {
              enabled: false
            },
            labels: categories,
            xaxis: {
              type: 'category'
            },
            yaxis: [{
                title: {
                  text: "Curah Hujan (mm)"
                }
              },
              {
                opposite: true,
                title: {
                  text: "Suhu & Kelembapan"
                }
              }
            ],
            colors: ['#FF0000', '#2eca6a', '#4154f1'],
            tooltip: {
              shared: true,
              intersect: false
            }
          }).render();
        });
      </script> --}}

    </main><!-- End #main -->

    @include('layouts.footer')

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    @include('layouts.script')

  </body>

</html>