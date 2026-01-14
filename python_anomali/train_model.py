import pandas as pd
import numpy as np
from sklearn.ensemble import IsolationForest
import joblib
import os
import db_config  # Import file koneksi yang kita buat tadi

# --- KONFIGURASI MODEL ---
MODEL_FILE = 'isoforest_model.pkl'
FEATURES = ['temperature', 'humidity', 'pressure', 'wind_speed', 'rainfall']

def get_real_data():
    """Mengambil data asli dari tabel data_aws"""
    conn = db_config.get_db_connection()
    if conn is None:
        return pd.DataFrame() # Kembalikan kosong jika gagal

    try:
        # Query mengambil kolom yang relevan
        query = f"SELECT {', '.join(FEATURES)} FROM data_aws"
        df = pd.read_sql(query, conn)
        conn.close()
        
        # Hapus data yang ada nilai NULL (error sensor)
        df = df.dropna()
        
        print(f"[INFO] Data Real berhasil diambil: {len(df)} baris.")
        return df
    except Exception as e:
        print(f"[ERROR] Gagal query data real: {e}")
        return pd.DataFrame()

def generate_synthetic_data():
    """
    Membuat data tiruan berdasarkan Statistik Buletin BMKG.
    Ini mengatasi masalah 'Cold Start' (data real masih sedikit).
    """
    # CONTOH DATA BULETIN (Silakan lengkapi Jan-Des sesuai data asli BMKG Banyuwangi)
    # Format: Min Temp, Max Temp, Min Hum, Max Hum
    stats_bulanan = [
        {'min_t': 23, 'max_t': 32, 'min_h': 65, 'max_h': 95}, # Jan
        {'min_t': 23, 'max_t': 33, 'min_h': 60, 'max_h': 90}, # Feb
        {'min_t': 22, 'max_t': 34, 'min_h': 55, 'max_h': 85}, # Mar (dst...)
    ]
    
    dfs = []
    np.random.seed(42)
    samples_per_month = 500 # Jumlah sampel per bulan
    
    for stat in stats_bulanan:
        df_month = pd.DataFrame({
            'temperature': np.random.uniform(stat['min_t'], stat['max_t'], samples_per_month),
            'humidity':    np.random.uniform(stat['min_h'], stat['max_h'], samples_per_month),
            # Asumsi parameter lain normal rata-rata
            'pressure':    np.random.normal(1010, 3, samples_per_month),
            'wind_speed':  np.random.uniform(0, 20, samples_per_month),
            'rainfall':    np.random.choice([0, 0, 0, 5, 20], samples_per_month)
        })
        dfs.append(df_month)
    
    combined_synth = pd.concat(dfs, ignore_index=True)
    print(f"[INFO] Data Sintetis dibuat: {len(combined_synth)} baris.")
    return combined_synth

def main():
    # 1. Ambil Data
    df_real = get_real_data()
    df_synth = generate_synthetic_data()

    # 2. Gabungkan (Hybrid Augmentation)
    if not df_real.empty:
        # Gabung data real dan sintetis
        df_train = pd.concat([df_synth, df_real], ignore_index=True)
    else:
        print("[WARN] Data real kosong, menggunakan full sintetis.")
        df_train = df_synth

    # 3. Training Isolation Forest
    # contamination=0.01 (Asumsi 1% data mungkin noise/anomali)
    clf = IsolationForest(n_estimators=100, contamination=0.01, random_state=42)
    clf.fit(df_train[FEATURES])

    # 4. Simpan Model
    # Simpan di folder yang sama dengan script ini
    current_dir = os.path.dirname(os.path.abspath(__file__))
    model_path = os.path.join(current_dir, MODEL_FILE)
    
    joblib.dump(clf, model_path)
    print(f"\n[SUCCESS] Model berhasil dilatih & disimpan di: {model_path}")
    print(f"Total Data Latih: {len(df_train)} baris.")

if __name__ == "__main__":
    main()