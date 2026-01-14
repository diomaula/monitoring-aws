# # config.py
# import mysql.connector
# import pandas as pd

# DB_CONFIG = {
#     "host": "localhost",
#     "user": "root",
#     "password": "",
#     "database": "tools-monitoring"
# }

# def get_connection():
#     return mysql.connector.connect(**DB_CONFIG)

# def get_laporan_harian():
#     conn = get_connection()

#     query = """
#         SELECT
#             aws_id,
#             date,
#             min_temperature,
#             max_temperature,
#             avg_temperature,
#             min_humidity,
#             max_humidity,
#             avg_humidity,
#             min_pressure,
#             max_pressure,
#             avg_pressure,
#             total_rainfall,
#             rainfall_max,
#             wind_speed_min,
#             wind_speed_max,
#             wind_speed_avg
#         FROM laporan_harian
#         WHERE avg_temperature IS NOT NULL
#     """

#     df = pd.read_sql(query, conn)
#     conn.close()

#     return df


# config.py
import pandas as pd
from sqlalchemy import create_engine

# Koneksi database MySQL
DB_USER = "root"
DB_PASS = ""
DB_HOST = "localhost"
DB_NAME = "tools-monitoring"

DB_URL = f"mysql+mysqlconnector://{DB_USER}:{DB_PASS}@{DB_HOST}/{DB_NAME}"
engine = create_engine(DB_URL)

def get_laporan_harian():
    """
    Mengambil data laporan harian dari MySQL
    """
    query = """
        SELECT
            aws_id,
            date,
            min_temperature,
            max_temperature,
            avg_temperature,
            min_humidity,
            max_humidity,
            avg_humidity,
            min_pressure,
            max_pressure,
            avg_pressure,
            total_rainfall,
            rainfall_max,
            wind_speed_min,
            wind_speed_max,
            wind_speed_avg
        FROM laporan_harian
        WHERE avg_temperature IS NOT NULL
    """
    df = pd.read_sql(query, engine)
    df['date'] = pd.to_datetime(df['date'])
    return df