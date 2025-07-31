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
              <div>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</div>
              <div>{{ \Carbon\Carbon::now()->format('H:i:s') }} (UTC)</div>
          </div>
      </div>
    </div><!-- End Page Title -->

    
    @if($online && isset($data['waktu']))
    <div class="data-grid">
        <div class="data-box">
            <strong>Kecepatan Angin (m/s)</strong>
            <span>{{ $data['windspeed'] }}</span>
        </div>
        <div class="data-box">
            <strong>Arah Angin (°)</strong>
            <span>{{ $data['winddir'] }}</span>
        </div>
        <div class="data-box">
            <strong>Suhu Udara (°C)</strong>
            <span>{{ $data['temp'] }}</span>
        </div>
        <div class="data-box">
            <strong>Kelembapan (%)</strong>
            <span>{{ $data['rh'] }}</span>
        </div>
        <div class="data-box">
            <strong>Tekanan Udara (hPa)</strong>
            <span>{{ $data['pressure'] }}</span>
        </div>
        <div class="data-box">
            <strong>Curah Hujan (mm)</strong>
            <span>{{ $data['rain'] }}</span>
        </div>
        <div class="data-box">
            <strong>Suhu Air (°C)</strong>
            <span>{{ $data['watertemp'] }}</span>
        </div>
        <div class="data-box">
            <strong>Tinggi Permukaan Air (m)</strong>
            <span>{{ $data['waterlevel'] }}</span>
        </div>
    </div>
    @else
    <p>Tidak ada data yang dapat ditampilkan.</p>
    @endif

  </main><!-- End #main -->

  @include('layouts.footer')

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  @include('layouts.script')

</body>

</html>