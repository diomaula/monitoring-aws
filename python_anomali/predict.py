import sys
import json
import numpy as np
import pandas as pd
import joblib
import os
from datetime import datetime

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODEL_DIR = os.path.join(BASE_DIR, "models")

try:
    # ============================
    # INPUT
    # ============================
    input_data = json.loads(sys.stdin.read())

    aws_id = input_data['aws_id']

    # ============================
    # LOAD MODEL & SCALER
    # ============================
    model_path = os.path.join(MODEL_DIR, f"model_aws_{aws_id}.pkl")
    scaler_path = os.path.join(MODEL_DIR, f"scaler_aws_{aws_id}.pkl")

    if not os.path.exists(model_path):
        raise Exception(f"Model tidak ditemukan untuk AWS {aws_id}")

    if not os.path.exists(scaler_path):
        raise Exception(f"Scaler tidak ditemukan untuk AWS {aws_id}")

    model = joblib.load(model_path)
    scaler = joblib.load(scaler_path)

    # ============================
    # PREPROCESSING
    # ============================
    timestamp = input_data['timestamp']
    dt = datetime.strptime(timestamp, "%Y-%m-%d %H:%M:%S")
    jam = dt.hour

    jam_sin = np.sin(2 * np.pi * jam / 24)
    jam_cos = np.cos(2 * np.pi * jam / 24)

    features = pd.DataFrame([{
        'jam_sin': jam_sin,
        'jam_cos': jam_cos,
        'temperature': input_data['temperature'],
        'humidity': input_data['humidity'],
        'pressure': input_data['pressure'],
        'watertemp': input_data['watertemp'],
        'waterlevel': input_data['waterlevel'],
        'solrad': input_data['solrad']
    }])

    # ============================
    # SCALING
    # ============================
    X_scaled = scaler.transform(features)

    # ============================
    # PREDIKSI
    # ============================
    score = model.decision_function(X_scaled)[0]
    prediction = model.predict(X_scaled)[0]

    result = {
        "score": float(score),
        "status": "ANOMALI" if prediction == -1 else "NORMAL"
    }

    print(json.dumps(result))

except Exception as e:
    import traceback
    print(json.dumps({
        "error": str(e),
        "trace": traceback.format_exc()
    }))



# import sys
# import json
# import numpy as np
# import pandas as pd
# import joblib
# import os

# MODEL_DIR = "models"

# try:
#     # ============================
#     # INPUT DARI LARAVEL
#     # ============================
#     input_data = json.loads(sys.argv[1])

#     aws_id = input_data['aws_id']

#     # ============================
#     # LOAD MODEL & SCALER
#     # ============================
#     model_path = os.path.join(MODEL_DIR, f"model_aws_{aws_id}.pkl")
#     scaler_path = os.path.join(MODEL_DIR, f"scaler_aws_{aws_id}.pkl")

#     model = joblib.load(model_path)
#     scaler = joblib.load(scaler_path)

#     # ============================
#     # PREPROCESSING (SAMA DENGAN TRAINING)
#     # ============================

#     # 🔥 Ambil datetime dari input
#     dt = pd.to_datetime(input_data['datetime'])

#     # 🔥 Round ke jam terdekat
#     dt_round = dt.round('H')

#     # 🔥 Ambil jam
#     jam = dt_round.hour

#     # 🔥 Transform ke sin & cos
#     jam_sin = np.sin(2 * np.pi * jam / 24)
#     jam_cos = np.cos(2 * np.pi * jam / 24)

#     # ============================
#     # BENTUK FEATURE
#     # ============================
#     features = [[
#         jam_sin,
#         jam_cos,
#         input_data['temperature'],
#         input_data['humidity'],
#         input_data['pressure'],
#         input_data['pancitemp'],
#         input_data['pancilevel'],
#         input_data['solrad']
#     ]]

#     # ============================
#     # SCALING
#     # ============================
#     X_scaled = scaler.transform(features)

#     # ============================
#     # PREDIKSI
#     # ============================
#     score = model.decision_function(X_scaled)[0]
#     prediction = model.predict(X_scaled)[0]  # -1 = anomali

#     result = {
#         "anomali_score": float(score),
#         "status": int(prediction == -1)
#     }

#     print(json.dumps(result))

# except Exception as e:
#     import traceback
#     print(json.dumps({
#         "error": str(e),
#         "trace": traceback.format_exc()
#     }))