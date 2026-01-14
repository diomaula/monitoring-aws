import mysql.connector
import sys

def get_db_connection():
    """
    Membuat koneksi ke Database MySQL Laravel.
    Sesuaikan kredensial di bawah ini dengan file .env Anda.
    """
    try:
        connection = mysql.connector.connect(
            host="127.0.0.1",      # Host database (biasanya 127.0.0.1 atau localhost)
            user="root",           # Username database
            password="",           # Password database
            database="tools-monitoring" # Nama database Anda
        )
        return connection
    except mysql.connector.Error as err:
        print(f"[DB Error] Gagal koneksi: {err}")
        return None