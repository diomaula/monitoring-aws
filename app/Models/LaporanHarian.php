<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanHarian extends Model
{
    protected $table = 'laporan_harian';

    protected $fillable = [
        'aws_id',
        'date',
        'min_temperature',
        'max_temperature',
        'avg_temperature',
        'min_humidity',
        'max_humidity',
        'avg_humidity',
        'min_pressure',
        'max_pressure',
        'avg_pressure',
        'total_rainfall',
        'rainfall_max',
        'rainy_days',
        'wind_speed_min',
        'wind_speed_max',
        'wind_speed_avg',
        'dominant_wind_direction',
    ];

    public function aws()
    {
        return $this->belongsTo(Aws::class, 'aws_id');
    }
}
