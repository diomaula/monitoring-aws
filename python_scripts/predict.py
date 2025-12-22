import sys
import os
import joblib
import numpy as np

# Matikan log tensorflow yang berisik
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3' 
import tensorflow as tf
from tensorflow.keras.models import load_model

# 1. Setup Path Otomatis
script_dir = os.path.dirname(os.path.abspath(__file__))
model_path = os.path.join(script_dir, 'model_cuaca.h5')
scaler_path = os.path.join(script_dir, 'scaler.gz')

try:
    # 2. Load Model & Scaler
    model = load_model(model_path)
    scaler = joblib.load(scaler_path)

    # 3. Ambil Input dari Laravel
    # Input berupa string: "24.5,25.1,26.0,..." (60 data dipisah koma)
    input_string = sys.argv[1] 
    input_list = [float(x) for x in input_string.split(',')]

    # Pastikan jumlah data pas 60 (sesuai training)
    # Jika kurang, padding dengan data terakhir. Jika lebih, potong.
    if len(input_list) < 60:
        input_list = [input_list[0]] * (60 - len(input_list)) + input_list
    elif len(input_list) > 60:
        input_list = input_list[-60:]

    # 4. Preprocessing & Prediksi
    input_array = np.array(input_list).reshape(-1, 1)
    scaled_input = scaler.transform(input_array)
    x_test = np.reshape(scaled_input, (1, 60, 1))

    prediction = model.predict(x_test, verbose=0)
    final_result = scaler.inverse_transform(prediction)

    # 5. Output ke Laravel (Hanya print Angka)
    print(f"{final_result[0][0]:.2f}")

except Exception as e:
    # Jika error, print 0 agar Laravel tidak crash
    print("0")