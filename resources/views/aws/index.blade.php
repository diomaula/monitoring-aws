<!DOCTYPE html>
<html lang="en">

@include('layouts.header')

<head>
    <style>
        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-section img {
            height: 50px;
        }

        .logo-section h1 {
            font-size: 20px;
            color: #003366;
            margin: 0;
        }

        .info-section {
            text-align: right;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-section div {
            margin: 2px 0;
        }

        .status {
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 0;
        }

        .online {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .offline {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .data-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .data-box strong {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            color: #555;
        }

        .data-box span {
            font-size: 20px;
            font-weight: bold;
        }

        .btn-back {
            display: inline-block;
            padding: 10px 16px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }

        .compass {
            width: 200px;
            height: 200px;
            position: relative;
            display: inline-block;
        }

        .compass-circle {
            width: 100%;
            height: 100%;
            border: 8px solid #ccc;
            border-radius: 50%;
            background: #1e3a8a;
            position: relative;
        }

        .compass-circle span {
            color: white;
            font-weight: bold;
            position: absolute;
            font-size: 14px;
        }

        /* Arah utama */
        .compass-circle .north {
            top: 5px;
            left: 50%;
            transform: translateX(-50%);
        }

        .compass-circle .south {
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
        }

        .compass-circle .west {
            top: 50%;
            left: 5px;
            transform: translateY(-50%);
        }

        .compass-circle .east {
            top: 50%;
            right: 5px;
            transform: translateY(-50%);
        }

        /* Arah tambahan */
        .compass-circle .ne {
            top: 25px;
            right: 25px;
            font-size: 12px;
        }

        .compass-circle .nw {
            top: 25px;
            left: 25px;
            font-size: 12px;
        }

        .compass-circle .se {
            bottom: 25px;
            right: 25px;
            font-size: 12px;
        }

        .compass-circle .sw {
            bottom: 25px;
            left: 25px;
            font-size: 12px;
        }

        /* Jarum kompas */
        .compass-arrow {
            width: 4px;
            height: 80px;
            background: red;
            position: absolute;
            top: 50%;
            left: 50%;
            transform-origin: bottom center;
            transform: translate(-50%, -100%) rotate(0deg);
            transition: transform 0.5s ease-in-out;
            /* animasi halus */
        }

        /* Titik tengah */
        .compass-center {
            width: 20px;
            height: 20px;
            background: #fff;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>

<body>
    @include('layouts.loading')

    @include('layouts.navbar')

    @include('layouts.sidebar')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>{{ $name }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
            <div class="header-section">
                <div class="status {{ $online ? 'online' : 'offline' }}">
                    Status: {{ $online ? 'Online' : 'Offline / Data Tidak Ditemukan' }}
                </div>
                <div class="info-section">
                    <div>{{ \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('l, d F Y') }}</div>
                    <div id="utc-clock">Memuat waktu...</div>
                    <div id="clock">Memuat waktu...</div>
                </div>
            </div>
        </div><!-- End Page Title -->

        @if($online && isset($data['waktu']))
        <div class="data-grid">
            <div class="data-box">
                <strong>Suhu Udara (°C)</strong>
                <span id="temp">{{ $data['temp'] }}</span>
            </div>
            <div class="data-box">
                <strong>Kelembapan (%RH)</strong>
                <span id="rh">{{ $data['rh'] }}</span>
            </div>
            <div class="data-box">
                <strong>Tekanan Udara (mbar)</strong>
                <span id="pressure">{{ $data['pressure'] }}</span>
            </div>
            <div class="data-box">
                <strong>Radiasi (w/m²)</strong>
                <span id="solrad">{{ $data['solrad'] }}</span>
            </div>
            <div class="data-box">
                <strong>Curah Hujan (mm)</strong>
                <span id="rain">{{ $data['rain'] }}</span>
            </div>
            <div class="data-box">
                <strong>Kecepatan Angin (m/s)</strong>
                <span id="windspeed">{{ $data['windspeed'] }}</span>
            </div>
            <div class="data-box">
                <strong>Suhu Air (°C)</strong>
                <span id="watertemp">{{ $data['watertemp'] }}</span>
            </div>
            <div class="data-box">
                <strong>Tinggi Permukaan Air (m)</strong>
                <span id="waterlevel">{{ $data['waterlevel'] }}</span>
            </div>
            <div class="data-box">
                <strong>Kecepatan Angin (knot)</strong>
                <span id="windspeed_knot">{{ $data['windspeed_knot'] }}</span>
            </div>
        </div>

        <div class="col-md-12 col-sm-6 mb-3">
            <div class="card info-card">
                <div class="card-body text-center">
                    <h6 class="card-title">Arah Angin (°)</h6>

                    <!-- Kompas -->
                    <div class="compass">
                        <div class="compass-circle">
                            <div class="compass-arrow" id="compass-arrow"></div>
                            <div class="compass-center"></div>

                            <!-- Label arah -->
                            <span class="north">N</span>
                            <span class="south">S</span>
                            <span class="west">W</span>
                            <span class="east">E</span>
                            <span class="ne">NE</span>
                            <span class="nw">NW</span>
                            <span class="se">SE</span>
                            <span class="sw">SW</span>
                        </div>
                    </div>

                    <h5 id="winddir" class="mt-3">{{ $data['winddir'] }}°</h5>
                </div>
            </div>
        </div>

            <!-- Chart -->
            <div class="card">
                <div class="card-body">
                    <!-- Tambahkan setelah grafik kelembapan -->
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-windrose@3"></script>

                    <h5 class="card-title mb-4">Grafik AWS (Curah Hujan, Suhu & Kelembapan)</h5>
        <!-- Chart -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Grafik AWS (Curah Hujan, Suhu & Kelembapan)</h5>

                <div class="mb-4">
                    {{-- <h6 class="fw-bold">Curah Hujan</h6> --}}
                    <div id="chartRainfall"></div>
                </div>

                <div class="mb-4">
                    {{-- <h6 class="fw-bold">Suhu</h6> --}}
                    <div id="chartTemperature"></div>
                </div>

                    <div>
                        {{-- <h6 class="fw-bold">Kelembapan</h6> --}}
                        <div id="chartHumidity"></div>
                    </div>
                    <div class="mb-4">
                        <h6 class="fw-bold">Diagram Arah & Kecepatan Angin (Windrose)</h6>
                    <div id="chartWindrose"></div>
                <div>
                    {{-- <h6 class="fw-bold">Kelembapan</h6> --}}
                    <div id="chartHumidity"></div>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", async () => {
                        try {
                            let code = "{{ $id }}";
                            let response = await fetch(`/chart-data/${code}`);
                            let result = await response.json();

                            // === Curah Hujan ===
                            new ApexCharts(document.querySelector("#chartRainfall"), {
                                series: [{
                                    name: 'Curah Hujan (mm)',
                                    data: result.rainfall
                                }],
                                chart: {
                                    type: 'area',
                                    height: 300,
                                    zoom: {
                                        enabled: true
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 2
                                },
                                markers: {
                                    size: 3
                                },
                                colors: ['#4154f1'],
                                dataLabels: {
                                    enabled: false
                                },
                                fill: {
                                    type: "gradient",
                                    gradient: {
                                        shadeIntensity: 1,
                                        opacityFrom: 0.3,
                                        opacityTo: 0.4,
                                        stops: [0, 90, 100]
                                    }
                                },
                                xaxis: {
                                    type: 'datetime',
                                    tickAmount: 7
                                },
                                yaxis: {
                                    title: {
                                        text: 'Curah Hujan (mm)',
                                        style: {
                                            fontSize: '12px'
                                        }
                                    }
                                },
                                tooltip: {
                                    x: {
                                        format: 'dd/MM/yy HH:mm'
                                    }
                                },
                                legend: {
                                    position: 'top',
                                    horizontalAlign: 'center'
                                }
                            }).render();

                            // === Suhu ===
                            new ApexCharts(document.querySelector("#chartTemperature"), {
                                series: [{
                                    name: 'Suhu (°C)',
                                    data: result.temp
                                }],
                                chart: {
                                    type: 'area',
                                    height: 300,
                                    zoom: {
                                        enabled: true
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 2
                                },
                                markers: {
                                    size: 3
                                },
                                colors: ['#FF0000'],
                                dataLabels: {
                                    enabled: false
                                },
                                fill: {
                                    type: "gradient",
                                    gradient: {
                                        shadeIntensity: 1,
                                        opacityFrom: 0.3,
                                        opacityTo: 0.4,
                                        stops: [0, 90, 100]
                                    }
                                },
                                xaxis: {
                                    type: 'datetime',
                                    tickAmount: 7
                                },
                                yaxis: {
                                    title: {
                                        text: 'Suhu (°C)',
                                        style: {
                                            fontSize: '12px'
                                        }
                                    }
                                },
                                tooltip: {
                                    x: {
                                        format: 'dd/MM/yy HH:mm'
                                    }
                                },
                                legend: {
                                    position: 'top',
                                    horizontalAlign: 'center'
                                }
                            }).render();

                            // === Kelembapan ===
                            new ApexCharts(document.querySelector("#chartHumidity"), {
                                series: [{
                                    name: 'Kelembapan (%)',
                                    data: result.humidity
                                }],
                                chart: {
                                    type: 'area',
                                    height: 300,
                                    zoom: {
                                        enabled: true
                                    }
                                },
                                stroke: {
                                    curve: 'smooth',
                                    width: 2
                                },
                                markers: {
                                    size: 3
                                },
                                colors: ['#2eca6a'],
                                dataLabels: {
                                    enabled: false
                                },
                                fill: {
                                    type: "gradient",
                                    gradient: {
                                        shadeIntensity: 1,
                                        opacityFrom: 0.3,
                                        opacityTo: 0.4,
                                        stops: [0, 90, 100]
                                    }
                                },
                                xaxis: {
                                    type: 'datetime',
                                    tickAmount: 7
                                },
                                yaxis: {
                                    title: {
                                        text: 'Kelembapan (%)',
                                        style: {
                                            fontSize: '12px'
                                        }
                                    }
                                },
                                tooltip: {
                                    x: {
                                        format: 'dd/MM/yy HH:mm'
                                    }
                                },
                                legend: {
                                    position: 'top',
                                    horizontalAlign: 'center'
                                }
                            }).render();

                                // === Windrose (RADAR CHART) ===
                                if (result.windrose) {
                                    new ApexCharts(document.querySelector("#chartWindrose"), {
                                        series: [{
                                            name: 'Kecepatan Angin (m/s)',
                                            data: [
                                                { x: 'N', y: result.windrose.N ?? 0 },
                                                { x: 'NE', y: result.windrose.NE ?? 0 },
                                                { x: 'E', y: result.windrose.E ?? 0 },
                                                { x: 'SE', y: result.windrose.SE ?? 0 },
                                                { x: 'S', y: result.windrose.S ?? 0 },
                                                { x: 'SW', y: result.windrose.SW ?? 0 },
                                                { x: 'W', y: result.windrose.W ?? 0 },
                                                { x: 'NW', y: result.windrose.NW ?? 0 },
                                            ]
                                        }],
                                        chart: {
                                            type: 'radar',
                                            height: 400,
                                            toolbar: { show: false }
                                        },
                                        title: {
                                            text: 'Diagram Arah & Kecepatan Angin (Windrose)',
                                            align: 'center'
                                        },
                                        xaxis: {
                                            categories: ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'],
                                            labels: { style: { colors: '#333', fontWeight: 600 } }
                                        },
                                        yaxis: {
                                            show: true,
                                            labels: {
                                                formatter: val => val.toFixed(1) + " m/s"
                                            }
                                        },
                                        stroke: { width: 2 },
                                        fill: { opacity: 0.4, colors: ['#008FFB'] },
                                        markers: { size: 4 },
                                        colors: ['#008FFB']
                                    }).render();
                                } else {
                                    console.warn("Data windrose tidak tersedia.");
                                }

                            } catch (e) {
                                console.error("Gagal memuat data chart:", e);
                            }
                        });
                    </script>
                </div>
                        } catch (e) {
                            console.error("Gagal memuat data chart:", e);
                        }
                    });
                </script>
            </div>
        </div>


        <script>
            function updateUtcClock() {
                let now = new Date();
                let utcHours = now.getUTCHours().toString().padStart(2, '0');
                let utcMinutes = now.getUTCMinutes().toString().padStart(2, '0');
                let utcSeconds = now.getUTCSeconds().toString().padStart(2, '0');
                document.getElementById("utc-clock").innerText = `${utcHours}:${utcMinutes}:${utcSeconds} UTC`;
            }

            setInterval(updateUtcClock, 1000);
            updateUtcClock();

            // jam WIB realtime
            function updateClock() {
                const now = new Date();
                const options = {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                };
                const time = now.toLocaleTimeString('en-GB', options);
                document.getElementById('clock').textContent = `${time} WIB`;
            }
            setInterval(updateClock, 1000);
            updateClock(); // panggilan pertama

            // Kompas
            function updateCompass(angle) {
                const arrow = document.getElementById('compass-arrow');
                if (arrow) {
                    arrow.style.transform = `translate(-50%, -100%) rotate(${angle}deg)`;
                }
            }

            // Ambil stationId dari Blade
            const stationId = "{{ $id }}";

            // Ambil data cuaca
            async function fetchWeatherData() {
                try {
                    const stationId = "{{ $id }}";
                    const response = await fetch(`/aws/${stationId}`, {
                        headers: {
                            "Accept": "application/json"
                        }
                    });

                    if (!response.ok) {
                        console.error('Gagal fetch:', response.status);
                        return;
                    }

                    const data = await response.json();

                    if (!data) {
                        console.warn('Data kosong');
                        return;
                    }

                    // Update nilai DOM
                    document.getElementById('windspeed').textContent = data.windspeed ?? '-';
                    document.getElementById('winddir').textContent = (data.winddir ?? '-') + '°';
                    document.getElementById('temp').textContent = data.temp ?? '-';
                    document.getElementById('rh').textContent = data.rh ?? '-';
                    document.getElementById('pressure').textContent = data.pressure ?? '-';
                    document.getElementById('rain').textContent = data.rain ?? '-';
                    document.getElementById('watertemp').textContent = data.watertemp ?? '-';
                    document.getElementById('waterlevel').textContent = data.waterlevel ?? '-';

                    // Update kompas
                    if (data.winddir !== undefined && data.winddir !== null) {
                        updateCompass(data.winddir);
                    }

                } catch (error) {
                    console.error('Gagal memuat data cuaca:', error);
                }
            }

            // 🚀 Initial fetch + auto refresh tiap 1 menit
            fetchWeatherData();
            setInterval(fetchWeatherData, 60000);
        </script>

        @else
        <p>Tidak ada data yang dapat ditampilkan.</p>
        @endif

    </main><!-- End #main -->

    @include('layouts.footer')

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    @include('layouts.script')

</body>

</html>