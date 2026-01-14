import sys
import joblib
import pandas as pd
import json
import os
import warnings

# Matikan warning agar output JSON bersih
warnings.filterwarnings("ignore")

MODEL_FILE = 'isoforest_model.pkl'
FEATURES = ['temperature', 'humidity', 'pressure', 'wind_speed', 'rainfall']

def main():
    # 1. Load Model
    current_dir = os.path.dirname(os.path.abspath(__file__))
    model_path = os.path.join(current_dir, MODEL_FILE)

    try:
        clf = joblib.load(model_path)
    except FileNotFoundError:
        # Jika model belum dibuat, kirim error JSON
        print(json.dumps({"status": "error", "message": "Model belum dilatih. Jalankan train_model.py dulu."}))
        return

    # 2. Tangkap Input dari Laravel (Command Line Arguments)
    # Urutan argumen harus sesuai dengan urutan di Laravel Process
    try:
        if len(sys.argv) < 6:
            raise ValueError("Input data kurang lengkap")

        input_data = [
            float(sys.argv[1]), # Temp
            float(sys.argv[2]), # Hum
            float(sys.argv[3]), # Press
            float(sys.argv[4]), # Wind
            float(sys.argv[5])  # Rain
        ]
        
        # Buat DataFrame single row
        df_new = pd.DataFrame([input_data], columns=FEATURES)

        # 3. Lakukan Prediksi
        # Output: 1 (Normal), -1 (Anomali)
        prediction_label = clf.predict(df_new)[0]
        
        # Output: Score negatif (makin kecil makin anomali)
        anomaly_score = clf.decision_function(df_new)[0]

        # 4. Format Output JSON
        status_text = "Normal" if prediction_label == 1 else "Anomali"
        
        result = {
            "status": "success",
            "prediction": status_text,
            "score": round(anomaly_score, 4),
            "input": input_data
        }
        
        # Print JSON ke stdout (ini yang dibaca Laravel)
        print(json.dumps(result))

    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))

if __name__ == "__main__":
    main()