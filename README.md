# Pengembangan Fitur Deteksi Data Anomali pada Sistem Monitoring Automatic Weather Station BMKG Banyuwangi Menggunakan Algoritma Isolation Forest

Repository ini berisi source code Tugas Akhir yang berjudul **"Pengembangan Fitur Deteksi Data Anomali pada Sistem Monitoring Automatic Weather Station BMKG Banyuwangi Menggunakan Algoritma Isolation Forest"**.

---

## Deskripsi Proyek

Automatic Weather Station (AWS) merupakan perangkat pengamatan cuaca otomatis yang digunakan untuk mengukur berbagai parameter meteorologi seperti suhu udara, kelembapan udara, tekanan udara, suhu air, tinggi muka air, dan radiasi matahari.

Dalam operasionalnya, AWS dapat mengalami kondisi tidak normal akibat gangguan sensor, kerusakan perangkat, maupun faktor lingkungan. Selama ini proses evaluasi kondisi AWS masih dilakukan secara manual sehingga membutuhkan waktu yang cukup lama dan bergantung pada pengalaman teknisi.

Fitur ini dikembangkan untuk membantu teknisi dalam melakukan evaluasi kondisi AWS secara otomatis menggunakan algoritma **Isolation Forest** sehingga anomali pada data pengamatan dapat dideteksi lebih cepat.

Fitur terdiri dari dua komponen utama:

1. **Aplikasi Web (Laravel)** sebagai dashboard monitoring dan pengelolaan data AWS.
2. **Model Machine Learning (Python)** menggunakan algoritma Isolation Forest untuk mendeteksi anomali pada data meteorologi.

---

## Fitur Sistem

### Teknisi

- Login
- Dashboard monitoring kondisi seluruh AWS
- Monitoring kondisi AWS secara realtime
- Menampilkan status **Normal** atau **Anomali**
- Detail kondisi setiap AWS
- Grafik parameter meteorologi
- Riwayat evaluasi kondisi AWS
- Pengaturan nilai Sensitivitas 
- Menampilkan nilai Anomaly Score
- Logout

### Sistem

- Mengambil data AWS secara otomatis melalui API BMKG menggunakan cron job server
- Melakukan preprocessing data
- Melakukan prediksi menggunakan model Isolation Forest 
- Menghitung Anomaly Score
- Menentukan status Normal atau Anomali
- Menyimpan hasil prediksi ke database
- Menampilkan hasil evaluasi pada dashboard

---

## Alur Machine Learning

Model yang digunakan adalah **Isolation Forest**.

Tahapan proses:

1. Pengambilan data AWS
2. Preprocessing data
3. Transformasi fitur waktu menjadi:
   - jam
   - jam_sin
   - jam_cos
4. Normalisasi data menggunakan StandardScaler
5. Pelatihan model Isolation Forest
6. Penyimpanan model (.pkl)
7. Prediksi data baru
8. Perhitungan Anomaly Score
9. Penentuan status:
   - Normal
   - Anomali

---

## Teknologi yang Digunakan

### Backend (Laravel)

| Kategori | Teknologi |
|-----------|-----------|
| Framework | Laravel 10 |
| Bahasa | PHP 8.2 |
| Database | MySQL |
| Frontend | Blade |
| CSS Framework | Bootstrap 5 |
| Chart | Chart.js |
| HTTP Client | Laravel HTTP Client |

### Machine Learning

| Kategori | Teknologi |
|-----------|-----------|
| Bahasa | Python 3.10 |
| Library | pandas |
| Library | numpy |
| Library | scikit-learn |
| Library | joblib |
| Library | Isolation Forest |

### Deployment

- Shared Hosting (cPanel)
- Python 3.10
- Laravel Scheduler (Cron Job)

---

## Struktur Database

### Tabel `data_aws`

| Kolom | Deskripsi |
|--------|-----------|
| id | Primary Key |
| aws_id | ID AWS |
| timestamp | Waktu Pengamatan |
| temperature | Suhu Udara |
| humidity | Kelembapan Udara |
| pressure | Tekanan Udara |
| watertemp | Suhu Air |
| waterlevel | Tinggi Muka Air |
| solrad | Radiasi Matahari |
| status | Status Hasil Prediksi |
| anomaly_score | Nilai Skor Anomali |
| created_at | Waktu Dibuat |
| updated_at | Waktu Diperbarui |

### Tabel `aws`

| Kolom | Deskripsi |
|--------|-----------|
| id | Primary Key |
| name | Nama AWS |
| location | Lokasi AWS |

### Tabel `users`

| Kolom | Deskripsi |
|--------|-----------|
| id | Primary Key |
| name | Nama Pengguna |
| email | Email |
| password | Password |

---

# Instalasi

## Persyaratan

- Git
- PHP 8.2+
- Composer
- MySQL
- Python 3.10+
- Laragon / XAMPP (Opsional)

---

## 1. Clone Repository

```bash
git clone https://github.com/TRPL-JBI/TA2026-362258302087-DioRizaMaula.git

cd evaluasi-aws
```

---

## 2. Setup Laravel

Install dependency

```bash
composer install
```

Salin file environment

```bash
cp .env.example .env
```

Atur konfigurasi database

```env
APP_NAME="Evaluasi AWS"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=evaluasi_aws
DB_USERNAME=root
DB_PASSWORD=
```

Generate application key

```bash
php artisan key:generate
```

Migrasi database

```bash
php artisan migrate
```

Jalankan server

```bash
php artisan serve
```

---

## 3. Setup Python

Masuk ke folder Python

```bash
cd python_anomali
```

Install dependency

```bash
pip install -r requirements.txt
```

---

## 4. Training Model

```bash
python train_model.py
```

Hasil training akan menghasilkan file model berikut.

```
models/
├── model_aws_1.pkl
├── model_aws_2.pkl
├── model_aws_3.pkl
├── scaler_aws_1.pkl
├── scaler_aws_2.pkl
└── scaler_aws_3.pkl
```

---

## 5. Prediksi

```bash
python predict.py
```

Script ini dipanggil secara otomatis oleh Laravel untuk melakukan evaluasi kondisi AWS.

---

## 6. Scheduler

Untuk mengambil data AWS secara otomatis jalankan

```bash
php artisan schedule:work
```

atau

```bash
php artisan aws:fetch-hourly
```

Pada server production gunakan Cron Job

```bash
* * * * * php /path/to/artisan schedule:run
```

---

## Struktur Direktori

```
evaluasi-aws/

├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── python_anomali/
│   ├── train_model.py
│   ├── predict.py
│   ├── models/
│   └── requirements.txt
├── .env
└── README.md
```

---

## Alur Sistem

```
API BMKG
      │
      ▼
Laravel Fetch Data
      │
      ▼
Database MySQL
      │
      ▼
Python Preprocessing
      │
      ▼
StandardScaler
      │
      ▼
Isolation Forest
      │
      ▼
Anomaly Score
      │
      ▼
Status Normal / Anomali
      │
      ▼
Dashboard Monitoring
```

---

## Pengembang

| | |
|---|---|
| **Nama** | Dio Riza Maula |
| **Program Studi** | Sarjana Terapan Teknologi Rekayasa Perangkat Lunak |
| **Jurusan** | Bisnis dan Informatika |
| **Institusi** | Politeknik Negeri Banyuwangi |

### Kontak
Email: **diomaula10@gmail.com**

---

## Lisensi

Proyek ini dibuat untuk keperluan Tugas Akhir di Politeknik Negeri Banyuwangi dan studi kasus di BMKG Banyuwangi. Penggunaan di luar keperluan akademik mohon menghubungi pengembang terlebih dahulu.
