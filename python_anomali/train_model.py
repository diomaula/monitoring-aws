import pandas as pd
import numpy as np
from sklearn.ensemble import IsolationForest
from sklearn.preprocessing import StandardScaler
import joblib
import os

# ============================
# CONFIG
# ============================
FILE_PATH = "data/data_aws_januari_clean.xlsx"
MODEL_DIR = "models"

os.makedirs(MODEL_DIR, exist_ok=True)

# ============================
# LOAD DATA
# ============================
df = pd.read_excel(FILE_PATH)

# ============================
# VALIDASI KOLOM
# ============================
required_columns = [
    'aws_id', 'timestamp', 'temperature', 'humidity',
    'pressure', 'watertemp', 'waterlevel', 'solrad'
]

for col in required_columns:
    if col not in df.columns:
        raise Exception(f"Kolom '{col}' tidak ditemukan")

# ============================
# PREPROCESSING
# ============================

# 🔥 convert timestamp
df['timestamp'] = pd.to_datetime(df['timestamp'], errors='coerce')

# 🔥 hapus data invalid
df = df.dropna(subset=['timestamp'])

# 🔥 ambil jam dari timestamp
df['jam'] = df['timestamp'].dt.hour

# 🔥 transform jam → sin & cos
df['jam_sin'] = np.sin(2 * np.pi * df['jam'] / 24)
df['jam_cos'] = np.cos(2 * np.pi * df['jam'] / 24)

# ============================
# FITUR
# ============================
features = [
    'jam_sin',
    'jam_cos',
    'temperature',
    'humidity',
    'pressure',
    'watertemp',
    'waterlevel',
    'solrad'
]

# ============================
# GROUP BY AWS
# ============================
grouped = df.groupby('aws_id')

print("=== MULAI TRAINING ===")

for aws_id, data in grouped:
    print(f"\nTraining model untuk AWS ID: {aws_id}")

    # ambil fitur & bersihkan missing
    X = data[features].dropna()

    # validasi jumlah data
    if len(X) < 50:
        print(f"Data terlalu sedikit untuk AWS {aws_id}, dilewati.")
        continue

    # ============================
    # SCALING
    # ============================
    scaler = StandardScaler()
    X_scaled = scaler.fit_transform(X)

    # ============================
    # TRAIN MODEL
    # ============================
    model = IsolationForest(
        n_estimators=100,
        random_state=42
    )

    model.fit(X_scaled)

    # ============================
    # SIMPAN MODEL & SCALER
    # ============================
    model_path = os.path.join(MODEL_DIR, f"model_aws_{aws_id}.pkl")
    scaler_path = os.path.join(MODEL_DIR, f"scaler_aws_{aws_id}.pkl")

    joblib.dump(model, model_path)
    joblib.dump(scaler, scaler_path)

    print(f"✔ Model: {model_path}")
    print(f"✔ Scaler: {scaler_path}")

print("\n=== TRAINING SELESAI ===")



# import pandas as pd
# import numpy as np
# from sklearn.ensemble import IsolationForest
# from sklearn.preprocessing import StandardScaler
# import joblib
# import os

# # ============================
# # CONFIG
# # ============================
# FILE_PATH = "data/data_aws_januari.xlsx"
# MODEL_DIR = "models"

# os.makedirs(MODEL_DIR, exist_ok=True)

# # ============================
# # LOAD DATA
# # ============================
# df = pd.read_excel(FILE_PATH)

# # ============================
# # VALIDASI KOLOM
# # ============================
# required_columns = [
#     'aws_id', 'datetime', 'temperature', 'humidity',
#     'pressure', 'pancitemp', 'pancilevel', 'solrad'
# ]

# for col in required_columns:
#     if col not in df.columns:
#         raise Exception(f"Kolom '{col}' tidak ditemukan")

# # ============================
# # PREPROCESSING
# # ============================

# # 🔥 Pastikan datetime format benar
# df['datetime'] = pd.to_datetime(df['datetime'])

# # 🔥 Round ke jam terdekat (atasi menit tidak 00)
# df['datetime_round'] = df['datetime'].dt.round('H')

# # 🔥 Ambil jam dari datetime
# df['jam'] = df['datetime_round'].dt.hour

# # 🔥 Jika dalam 1 jam ada banyak data → ambil rata-rata
# df = df.groupby(['aws_id', 'datetime_round']).mean(numeric_only=True).reset_index()

# # 🔥 Transform jam → sin & cos
# df['jam_sin'] = np.sin(2 * np.pi * df['jam'] / 24)
# df['jam_cos'] = np.cos(2 * np.pi * df['jam'] / 24)

# # 🔥 Pilih fitur
# features = [
#     'jam_sin',
#     'jam_cos',
#     'temperature',
#     'humidity',
#     'pressure',
#     'pancitemp',
#     'pancilevel',
#     'solrad'
# ]

# # ============================
# # GROUP BY AWS
# # ============================
# grouped = df.groupby('aws_id')

# print("=== MULAI TRAINING ===")

# for aws_id, data in grouped:
#     print(f"\nTraining model untuk AWS ID: {aws_id}")

#     # Ambil fitur & hapus missing
#     X = data[features].dropna()

#     # 🔥 Validasi minimal data
#     if len(X) < 50:
#         print(f"Data terlalu sedikit untuk AWS {aws_id}, dilewati.")
#         continue

#     # ============================
#     # SCALING
#     # ============================
#     scaler = StandardScaler()
#     X_scaled = scaler.fit_transform(X)

#     # ============================
#     # TRAIN MODEL
#     # ============================
#     model = IsolationForest(
#         n_estimators=100,
#         random_state=42
#     )

#     model.fit(X_scaled)

#     # ============================
#     # SIMPAN MODEL & SCALER
#     # ============================
#     model_path = os.path.join(MODEL_DIR, f"model_aws_{aws_id}.pkl")
#     scaler_path = os.path.join(MODEL_DIR, f"scaler_aws_{aws_id}.pkl")

#     joblib.dump(model, model_path)
#     joblib.dump(scaler, scaler_path)

#     print(f"✔ Model: {model_path}")
#     print(f"✔ Scaler: {scaler_path}")

# print("\n=== TRAINING SELESAI ===")