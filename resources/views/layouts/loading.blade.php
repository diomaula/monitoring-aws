<div id="loading-overlay" class="fixed inset-0 bg-white/80 z-[9999] hidden flex items-center justify-center">
    <div class="w-16 h-16 rounded-full bg-blue-500 animate-pulse"></div>
</div>

<script>
    // Tampilkan loader saat halaman mulai dimuat
    document.getElementById('loading-overlay').style.display = 'flex';

    // Hilangkan loader setelah semua resource halaman selesai
    window.addEventListener('load', function () {
        document.getElementById('loading-overlay').style.display = 'none';
    });

    // Fungsi untuk dipakai jika load data via fetch/axios
    function showLoader() {
        document.getElementById('loading-overlay').style.display = 'flex';
    }
    function hideLoader() {
        document.getElementById('loading-overlay').style.display = 'none';
    }
</script>