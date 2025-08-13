<!DOCTYPE html>
<html lang="en">

<head>
  @include('layouts.header')
  
  <style>
      body {
          font-family: 'Poppins', sans-serif;
          background: #f4f6f9;
          padding: 30px;
          color: #333;
      }

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
          <div id="clock">Memuat waktu...</div>
        </div>
        <!-- <script>
            function updateClock() {
                const now = new Date();

                // Konversi ke UTC+7 (WIB)
                const options = {
                    timeZone: 'Asia/Jakarta',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                };

                const time = now.toLocaleTimeString('en-GB', options); // Format H:i:s
                document.getElementById('clock').textContent = `${time} (WIB)`;
            }

            // Update setiap detik
            setInterval(updateClock, 1000);
            updateClock(); // panggil pertama kali saat load
        </script> -->
      </div>
    </div><!-- End Page Title -->

    
    @if($online && isset($data['waktu']))
    <div class="data-grid">
        <div class="data-box">
            <strong>Kecepatan Angin (m/s)</strong>
            <!-- <span>{{ $data['windspeed'] }}</span> -->
            <span id = "windspeed">{{ $data['windspeed'] }}</span>
        </div>
        <div class="data-box">
            <strong>Arah Angin (°)</strong>
            <!-- <span>{{ $data['winddir'] }}</span> -->
            <span id = "winddir">{{ $data['winddir'] }}</span>
        </div>
        <div class="data-box">
            <strong>Suhu Udara (°C)</strong>
            <!-- <span>{{ $data['temp'] }}</span> -->
            <span id = "temp">{{ $data['temp'] }}</span>
        </div>
        <div class="data-box">
            <strong>Kelembapan (%)</strong>
            <!-- <span>{{ $data['rh'] }}</span> -->
            <span id = "rh">{{ $data['rh'] }}</span>
        </div>
        <div class="data-box">
            <strong>Tekanan Udara (hPa)</strong>
            <!-- <span>{{ $data['pressure'] }}</span> -->
            <span id = "pressure">{{ $data['pressure'] }}</span>
        </div>
        <div class="data-box">
            <strong>Curah Hujan (mm)</strong>
            <!-- <span>{{ $data['rain'] }}</span> -->
            <span id = "rain">{{ $data['rain'] }}</span>
        </div>
        <div class="data-box">
            <strong>Suhu Air (°C)</strong>
            <!-- <span>{{ $data['watertemp'] }}</span> -->
            <span id = "watertemp">{{ $data['watertemp'] }}</span>
        </div>
        <div class="data-box">
            <strong>Tinggi Permukaan Air (m)</strong>
            <!-- <span>{{ $data['waterlevel'] }}</span> -->
            <span id = "waterlevel">{{ $data['waterlevel'] }}</span>
        </div>
    </div>
    <script>
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
            document.getElementById('clock').textContent = `${time} (WIB)`;
        }

        setInterval(updateClock, 1000);
        updateClock(); // initial call

        async function fetchWeatherData() {
            try {
                const response = await fetch('/api/stations');
                const responseData = await response.json();

                if (!response.ok) {
                    console.error('Gagal fetch:', response.status);
                    return;
                }

                const data = responseData[0]; // Ambil data pertama dari array

                // Pastikan data ada sebelum update
                if (!data) {
                    console.warn('Data kosong');
                    return;
                }

                // Update nilai DOM
                document.getElementById('windspeed').textContent = data.windspeed ?? '-';
                document.getElementById('winddir').textContent = data.winddir ?? '-';
                document.getElementById('temp').textContent = data.temp ?? '-';
                document.getElementById('rh').textContent = data.rh ?? '-';
                document.getElementById('pressure').textContent = data.pressure ?? '-';
                document.getElementById('rain').textContent = data.rain ?? '-';
                document.getElementById('watertemp').textContent = data.watertemp ?? '-';
                document.getElementById('waterlevel').textContent = data.waterlevel ?? '-';

            } catch (error) {
                console.error('Gagal memuat data cuaca:', error);
            }
        }
        fetchWeatherData(); // Initial fetch
        setInterval(fetchWeatherData, 60000); // Refresh setiap 1 menit
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