import sys
import os
import numpy as np
import joblib
import pandas as pd

# Matikan log tensorflow yang berisik agar tidak mengganggu output ke Laravel
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3' 
import tensorflow as tf
from tensorflow.keras.models import load_model

# 1. Setup Path (Agar script tahu dimana file model berada)
script_dir = os.path.dirname(os.path.abspath(__file__))
model_path = os.path.join(script_dir, 'model_cuaca.h5')
scaler_path = os.path.join(script_dir, 'scaler.gz')

# 2. Load Model & Scaler
try:
    model = load_model(model_path)
    scaler = joblib.load(scaler_path)
except Exception as e:
    print(f"Error loading model: {e}")
    sys.exit(1)

# 3. Ambil Input dari Laravel (String dipisah koma)
# Contoh input: "24.5,24.6,25.0,..."
if len(sys.argv) < 2:
    print("Error: No input data provided")
    sys.exit(1)

input_string = sys.argv[1] 

try:
    # Convert string ke list float
    input_list = [float(x) for x in input_string.split(',')]
    
    # Pastikan data ada 60 (sesuai training)
    if len(input_list) != 60:
        # Jika data kurang (misal awal monitoring), duplikasi data terakhir
        while len(input_list) < 60:
            input_list.insert(0, input_list[0])
        # Jika data lebih, ambil 60 terakhir
        input_list = input_list[-60:]

    # 4. Preprocessing (Sama persis saat training)
    input_array = np.array(input_list).reshape(-1, 1)
    scaled_input = scaler.transform(input_array)
    x_test = np.reshape(scaled_input, (1, scaled_input.shape[0], 1))

    # 5. Prediksi
    predicted_val = model.predict(x_test, verbose=0)
    final_prediction = scaler.inverse_transform(predicted_val)

    # Output HANYA angka hasil prediksi (agar bisa ditangkap Laravel)
    print(f"{final_prediction[0][0]:.2f}")

except Exception as e:
    print(f"Error prediction: {e}")
    sys.exit(1)