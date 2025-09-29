<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMKG | Gangguan Layanan API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #1565c0, #42a5f5);
            color: #fff;
            text-align: center;
            padding: 50px;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        .error-code {
            font-size: 80px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .error-message {
            font-size: 20px;
            margin-bottom: 15px;
        }
        .info {
            font-size: 16px;
            margin-bottom: 30px;
        }
        a {
            display: inline-block;
            padding: 12px 24px;
            background: #ffcc00;
            color: #000;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            background: #ffc107;
        }
        .logo {
            width: 90px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://www.bmkg.go.id/asset/img/logo-bmkg.png" alt="BMKG" class="logo">
        <div class="error-code">API ERROR</div>
        <div class="error-message">Layanan Data Cuaca Tidak Dapat Dihubungi</div>
        <div class="info">
            Mohon maaf, saat ini layanan API BMKG mengalami gangguan.<br>
            Silakan coba beberapa saat lagi.
        </div>
        <a href="{{ url('/') }}">⬅️ Kembali ke Beranda</a>
    </div>
</body>
</html>
