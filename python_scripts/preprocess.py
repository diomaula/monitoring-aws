# # preprocess.py
# import pandas as pd

# def label_kondisi(row):
#     """
#     Aturan pelabelan kondisi operasional AWS
#     0 = Normal
#     1 = Tidak Normal
#     """

#     # Temperatur (°C)
#     if row["avg_temperature"] < 10 or row["avg_temperature"] > 40:
#         return 1

#     # Kelembapan (%)
#     if row["avg_humidity"] < 20 or row["avg_humidity"] > 100:
#         return 1

#     # Tekanan udara (hPa)
#     if row["avg_pressure"] < 950 or row["avg_pressure"] > 1050:
#         return 1

#     # Kecepatan angin (m/s)
#     if row["wind_speed_avg"] < 0 or row["wind_speed_avg"] > 40:
#         return 1

#     # Curah hujan tidak logis
#     if row["total_rainfall"] < 0 or row["rainfall_max"] < 0:
#         return 1

#     return 0  # Normal


# def preprocess_data(df):
#     # Pilih fitur yang dipakai decision tree
#     features = [
#         "avg_temperature",
#         "avg_humidity",
#         "avg_pressure",
#         "wind_speed_avg",
#         "total_rainfall"
#     ]

#     df = df.dropna(subset=features)

#     # Buat label
#     df["label"] = df.apply(label_kondisi, axis=1)

#     X = df[features]
#     y = df["label"]

#     return X, y, df



# preprocess.py
import pandas as pd
from sklearn.preprocessing import StandardScaler

def preprocess_data(df):
    """
    Preprocessing data untuk anomaly detection
    - Memilih semua fitur numerik sesuai tabel laporan_prediksi
    - Scaling fitur untuk model Isolation Forest
    """
    # Daftar semua fitur numerik sesuai tabel Laravel
    features = [
        'min_temperature', 'max_temperature', 'avg_temperature',
        'min_humidity', 'max_humidity', 'avg_humidity',
        'min_pressure', 'max_pressure', 'avg_pressure',
        'total_rainfall', 'rainfall_max',
        'wind_speed_min', 'wind_speed_max', 'wind_speed_avg'
    ]

    # Pastikan kolom tersedia
    missing_cols = [col for col in features if col not in df.columns]
    if missing_cols:
        raise ValueError(f"Kolom berikut tidak ada di data: {missing_cols}")

    # Pilih fitur
    X = df[features].copy()

    # Scaling (opsional untuk anomaly detection)
    scaler = StandardScaler()
    X_scaled = scaler.fit_transform(X)

    # Kembalikan dataframe lengkap + X_scaled
    return X_scaled, df
