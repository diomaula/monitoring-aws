# # decision_tree.py
# from config import get_laporan_harian
# from preprocess import preprocess_data

# from sklearn.tree import DecisionTreeClassifier
# from sklearn.model_selection import train_test_split
# from sklearn.metrics import classification_report, confusion_matrix

# def main():
#     print("📥 Mengambil data laporan harian...")
#     df = get_laporan_harian()

#     print("⚙️ Preprocessing & labeling data...")
#     X, y, df_labeled = preprocess_data(df)

#     print("📊 Jumlah data:", len(df_labeled))
#     print("Label Normal:", (y == 0).sum())
#     print("Label Tidak Normal:", (y == 1).sum())

#     # Split data
#     X_train, X_test, y_train, y_test = train_test_split(
#         X, y, test_size=0.3, random_state=42
#     )

#     print("🌳 Melatih Decision Tree...")
#     model = DecisionTreeClassifier(
#         max_depth=4,
#         random_state=42
#     )
#     model.fit(X_train, y_train)

#     # Evaluasi
#     y_pred = model.predict(X_test)

#     print("\n📈 Confusion Matrix:")
#     print(confusion_matrix(y_test, y_pred))

#     print("\n📄 Classification Report:")
#     print(classification_report(y_test, y_pred, target_names=["Normal", "Tidak Normal"]))

#     print("\n✅ Training & evaluasi selesai.")

# if __name__ == "__main__":
#     main()


# decision_tree.py
from config import get_laporan_harian, engine
from preprocess import preprocess_data
from sklearn.ensemble import IsolationForest
from datetime import datetime

import pandas as pd

def main():
    print("📥 Mengambil data laporan harian...")
    df = get_laporan_harian()

    print("⚙️ Preprocessing data...")
    X, df_preprocessed = preprocess_data(df)

    print("🌳 Melatih model Anomaly Detection (Isolation Forest)...")
    model = IsolationForest(
        n_estimators=100,
        contamination=0.05,  # perkiraan 5% data potensi gangguan
        random_state=42
    )
    model.fit(X)

    # Prediksi: 1 = Normal, -1 = Potensi Gangguan
    pred = model.predict(X)
    df_preprocessed['status'] = pd.Series(pred).apply(lambda x: "Normal" if x == 1 else "Potensi Gangguan")

    # Tambahkan timestamps
    now = datetime.now()
    df_preprocessed['created_at'] = now
    df_preprocessed['updated_at'] = now

    # Simpan semua kolom + status + timestamps
    columns_to_save = [
        'aws_id', 'date',
        'min_temperature', 'max_temperature', 'avg_temperature',
        'min_humidity', 'max_humidity', 'avg_humidity',
        'min_pressure', 'max_pressure', 'avg_pressure',
        'total_rainfall', 'rainfall_max',
        'wind_speed_min', 'wind_speed_max', 'wind_speed_avg',
        'status', 'created_at', 'updated_at'
    ]

    df_to_save = df_preprocessed[columns_to_save]

    # Gunakan append supaya tabel lama & foreign key tetap ada
    df_to_save.to_sql(
        name='laporan_prediksi',
        con=engine,
        if_exists='append',
        index=False
    )

    print("\n📊 Ringkasan prediksi:")
    print(df_preprocessed['status'].value_counts())

    print("\n✅ Semua kolom terisi dan data tersimpan ke tabel 'laporan_prediksi'.")
    print("✅ Training & evaluasi selesai.")

if __name__ == "__main__":
    main()
