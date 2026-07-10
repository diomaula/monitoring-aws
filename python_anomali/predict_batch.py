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

    rows = json.loads(sys.stdin.read())

    results = []

    model_cache = {}
    scaler_cache = {}

    for row in rows:

        aws_id = row["aws_id"]

        # Load model & scaler sekali saja
        if aws_id not in model_cache:

            model_path = os.path.join(
                MODEL_DIR,
                f"model_aws_{aws_id}_001.pkl"
            )

            scaler_path = os.path.join(
                MODEL_DIR,
                f"scaler_aws_{aws_id}_001.pkl"
            )

            model_cache[aws_id] = joblib.load(model_path)
            scaler_cache[aws_id] = joblib.load(scaler_path)

        model = model_cache[aws_id]
        scaler = scaler_cache[aws_id]

        # Preprocessing waktu
        dt = datetime.strptime(
            row["timestamp"],
            "%Y-%m-%d %H:%M:%S"
        )

        jam = dt.hour

        features = pd.DataFrame([{
            "jam_sin": np.sin(2 * np.pi * jam / 24),
            "jam_cos": np.cos(2 * np.pi * jam / 24),
            "temperature": row["temperature"],
            "humidity": row["humidity"],
            "pressure": row["pressure"],
            "watertemp": row["watertemp"],
            "waterlevel": row["waterlevel"],
            "solrad": row["solrad"]
        }])

        # Scaling
        X_scaled = scaler.transform(features)

        # Prediksi
        score = float(
            model.decision_function(X_scaled)[0]
        )

        prediction = int(
            model.predict(X_scaled)[0]
        )

        status = (
            "ANOMALI"
            if prediction == -1
            else "NORMAL"
        )

        results.append({
            "id": row["id"],
            "score": score,
            "status": status
        })

    print(json.dumps(results))

except Exception as e:

    import traceback

    print(json.dumps({
        "error": str(e),
        "trace": traceback.format_exc()
    }))