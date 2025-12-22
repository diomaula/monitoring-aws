import pandas as pd
import numpy as np
from sklearn.preprocessing import MinMaxScaler
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense
import joblib

# 1. Load Data
# Pastikan path ini mengarah ke file CSV yang di-generate Laravel tadi
df = pd.read_csv('../storage/app/dataset_cuaca.csv') 

# Kita hanya ambil kolom 'suhu' sebagai contoh prediksi (Univariate Time Series)
data = df['temperature'].values.reshape(-1, 1)

# 2. Preprocessing (Normalisasi data ke range 0-1 agar AI mudah belajar)
scaler = MinMaxScaler(feature_range=(0, 1))
scaled_data = scaler.fit_transform(data)

# Simpan Scaler untuk dipakai nanti saat prediksi
joblib.dump(scaler, 'scaler.gz') 

# 3. Split Data menjadi X (Input) dan Y (Target)
# Logika: Gunakan 60 data jam terakhir untuk memprediksi jam berikutnya
prediction_days = 60 

x_train = []
y_train = []

for i in range(prediction_days, len(scaled_data)):
    x_train.append(scaled_data[i-prediction_days:i, 0])
    y_train.append(scaled_data[i, 0])

x_train, y_train = np.array(x_train), np.array(y_train)
x_train = np.reshape(x_train, (x_train.shape[0], x_train.shape[1], 1))

# 4. Bangun Model LSTM
model = Sequential()
model.add(LSTM(units=50, return_sequences=True, input_shape=(x_train.shape[1], 1)))
model.add(LSTM(units=50, return_sequences=False))
model.add(Dense(units=25))
model.add(Dense(units=1)) # Output prediksi suhu

model.compile(optimizer='adam', loss='mean_squared_error')

# 5. Training
print("Sedang melatih model AI...")
model.fit(x_train, y_train, batch_size=1, epochs=1) # Naikkan epochs untuk hasil lebih akurat

# 6. Simpan Model
model.save('model_cuaca.h5')
print("Model berhasil disimpan sebagai model_cuaca.h5")