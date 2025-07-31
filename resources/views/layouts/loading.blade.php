<!-- resources/views/layouts/loading.blade.php -->
<div id="loading-overlay">
    <div class="spinner"></div>
</div>

<style>
#loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(255,255,255,0.8);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}
.spinner {
    border: 8px solid #f3f3f3;
    border-top: 8px solid #3498db;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
}
@keyframes spin { 
    0% { transform: rotate(0deg); } 
    100% { transform: rotate(360deg); } 
}
</style>

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
