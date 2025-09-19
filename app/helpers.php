<?php

if (!function_exists('formatNumber')) {
    function formatNumber($value, $noRound = false)
    {
        if ($value === null || $value === '') {
            return 0;
        }

        // Kalau khusus (tidak boleh dibulatkan)
        if ($noRound) {
            return number_format($value, 1, '.', '');
        }

        // Pecahan desimal
        $decimal = $value - floor($value);

        // Jika >= 0.5 bulatkan ke atas
        if ($decimal >= 0.5) {
            return round($value);
        }

        // Kalau < 0.5 tampilkan 1 angka di belakang koma
        return number_format($value, 1, '.', '');
    }
}
