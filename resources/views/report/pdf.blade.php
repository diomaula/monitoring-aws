<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <title>Laporan Bulanan PDF</title>
        <style>
            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 12px;
            }

            /* --- Kop Surat --- */
            .kop-surat {
                width: 100%;
                text-align: center;
            }

            .kop-table {
                border: none;
            }

            .kop-table td {
                border: none;
                padding: 0;
                /* opsional supaya rapat */
            }

            .kop-left {
                width: 120px;
                text-align: center;
                vertical-align: middle;
            }

            .kop-left img {
                width: 170px;
                height: auto;
            }

            .kop-right {
                text-align: center;
                vertical-align: middle;
            }

            .kop-right .judul-utama {
                font-size: 13px;
                font-weight: bold;
            }

            .kop-right .judul-sub {
                font-size: 18px;
                font-weight: bold;
            }

            .kop-right p {
                margin: 2px 0;
                line-height: 1.3;
                font-size: 12px;
            }

            .garis {
                border-bottom: 3px solid #000;
                margin-top: 5px;
            }

            .garis-tipis {
                border-bottom: 1px solid #000;
                margin-top: 1px;
                margin-bottom: 20px;
            }

            /* --- Judul Laporan --- */
            .laporan-title {
                text-align: center;
                margin: 15px 0 3px 0;
                font-size: 16px;
                font-weight: 600;
                text-transform: uppercase;
                font-family: "Times New Roman", serif;
                letter-spacing: 0.5px;
            }

            .laporan-subtitle {
                text-align: center;
                font-size: 12px;
                font-style: italic;
                color: #666;
                margin-bottom: 8px;
                font-family: "Times New Roman", serif;
            }

            .laporan-line {
                width: 65%;
                margin: 0 auto 20px auto;
                border-bottom: 2px solid #000;
                position: relative;
            }

            .laporan-line::after {
                content: "";
                position: absolute;
                left: 10%;
                right: 10%;
                bottom: -3px;
                border-bottom: 1px solid #000;
            }

            /* --- Tabel Data --- */
            table {
                border-collapse: collapse;
                width: 100%;
                margin-top: 10px;
                font-size: 12px;
            }

            table,
            th,
            td {
                border: 1px solid black;
            }

            th,
            td {
                padding: 6px;
            }

            th {
                background-color: #cfe2ff;
                font-weight: bold;
                text-align: center;
            }

            td {
                text-align: center;
            }

            td.parameter {
                text-align: left;
                padding-left: 8px;
            }

            tbody tr:nth-child(odd) {
                background-color: #f9f9f9;
            }

            /* --- TTD --- */
            .ttd {
                margin-top: 30px;
                width: 100%;
                display: flex;
                justify-content: flex-end;
            }

            .ttd .blok {
                text-align: right;
            }

            .ttd img {
                width: 100px;
                margin: 5px 0;
            }
        </style>
    </head>

    <body>
        {{-- HEADER --}}
        <div class="kop-surat">
            <table class="kop-table">
                <tr>
                    <td class="kop-left">
                        <img src="{{ public_path('assets/img/bmkg1.png') }}" alt="Logo BMKG"><br>
                    </td>
                    <td class="kop-right">
                        <div class="judul-utama">BADAN METEOROLOGI, KLIMATOLOGI, DAN GEOFISIKA</div>
                        <div class="judul-sub">STASIUN METEOROLOGI KELAS III BANYUWANGI</div>
                        <p>
                            Jl. Jaksa Agung Suprapto No. 152 Banyuwangi, Kode Pos: 68425,
                            Telp: (0333) 421888 / 410088,
                            E-mail: stamet.banyuwangi436911@gmail.com; met_987@yahoo.com; stamet.banyuwangi@bmkg.go.id,
                            Website: www.stamet-banyuwangi.bmkg.go.id
                        </p>
                    </td>
                </tr>
            </table>
            <div class="garis"></div>
            <div class="garis-tipis"></div>
        </div>

        {{-- ISI LAPORAN --}}
        <h3 class="laporan-title">Laporan Bulanan AWS (Automatic Weather Station)</h3>
        <p class="laporan-subtitle">Per {{ $bulanNama }} {{ $tahun }}</p>
        <div class="laporan-line"></div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Parameter</th>
                    <th>Nilai</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td class="parameter">Suhu Minimum</td>
                    <td>{{ $suhuMin }}</td>
                    <td>°C</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td class="parameter">Suhu Maksimum</td>
                    <td>{{ $suhuMax }}</td>
                    <td>°C</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td class="parameter">Suhu Rata-rata</td>
                    <td>{{ $suhuAvg }}</td>
                    <td>°C</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td class="parameter">Kelembapan Minimum</td>
                    <td>{{ $kelembapanMin }}</td>
                    <td>%</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td class="parameter">Kelembapan Maksimum</td>
                    <td>{{ $kelembapanMax }}</td>
                    <td>%</td>
                </tr>
                <tr>
                    <td>6</td>
                    <td class="parameter">Kelembapan Rata-rata</td>
                    <td>{{ $kelembapanAvg }}</td>
                    <td>%</td>
                </tr>
                <tr>
                    <td>7</td>
                    <td class="parameter">Tekanan Minimum</td>
                    <td>{{ $tekananMin }}</td>
                    <td>hPa</td>
                </tr>
                <tr>
                    <td>8</td>
                    <td class="parameter">Tekanan Maksimum</td>
                    <td>{{ $tekananMax }}</td>
                    <td>hPa</td>
                </tr>
                <tr>
                    <td>9</td>
                    <td class="parameter">Tekanan Rata-rata</td>
                    <td>{{ $tekananAvg }}</td>
                    <td>hPa</td>
                </tr>
                <tr>
                    <td>10</td>
                    <td class="parameter">Total Curah Hujan</td>
                    <td>{{ $curahHujan }}</td>
                    <td>mm</td>
                </tr>
                <tr>
                    <td>11</td>
                    <td class="parameter">Kecepatan Angin Rata-rata</td>
                    <td>{{ $kecepatanAngin }}</td>
                    <td>m/s</td>
                </tr>
                <tr>
                    <td>12</td>
                    <td class="parameter">Arah Angin Dominan</td>
                    <td>{{ $arahAngin }}</td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>

        {{-- TTD --}}
        <div class="ttd">
            <div class="blok">
                <p>Banyuwangi, {{ $tanggalRilis }}</p>
                <p>Kepala Stasiun,</p>
                <!-- <img src="{{ public_path('assets/img/qr-bmkg.png') }}" alt="QR Code"> -->
                <br><br>
                <p><strong>Teguh Tri Susanto</strong></p>
            </div>
        </div>
    </body>

</html>