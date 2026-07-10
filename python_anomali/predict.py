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

    input_data = json.loads(sys.stdin.read())

    aws_id = int(input_data['aws_id'])

    # Load model dan scaler
    model_path = os.path.join(MODEL_DIR, f"model_aws_{aws_id}_001.pkl")
    scaler_path = os.path.join(MODEL_DIR, f"scaler_aws_{aws_id}_001.pkl")

    if not os.path.exists(model_path):
        raise Exception(f"Model tidak ditemukan untuk AWS {aws_id}")

    if not os.path.exists(scaler_path):
        raise Exception(f"Scaler tidak ditemukan untuk AWS {aws_id}")

    model = joblib.load(model_path)
    scaler = joblib.load(scaler_path)

    # Preprocessing data
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

    # Scaling fitur
    X_scaled = scaler.transform(features)

    # Prediksi anomali
    score = float(model.decision_function(X_scaled)[0])
    prediction = int(model.predict(X_scaled)[0])

    # Menentukan status
    result = {
        "score": round(score, 6),
        "status": (
            "ANOMALI"
            if prediction == -1
            else "NORMAL"
        )
    }

    print(json.dumps(result))

except Exception as e:
    import traceback

    print(json.dumps({
        "error": str(e),
        "trace": traceback.format_exc()
    }))