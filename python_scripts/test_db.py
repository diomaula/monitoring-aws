import mysql.connector
from config import DB_CONFIG

conn = mysql.connector.connect(**DB_CONFIG)
print("Koneksi database berhasil")
conn.close()
