<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAws extends Model
{
    use HasFactory;

    protected $table = 'data_aws';

    protected $casts = [
        'timestamp'       => 'datetime',
        'temperature'     => 'float',
        'humidity'        => 'float',
        'pressure'        => 'float',
        'rainfall'        => 'float',
        'wind_speed'      => 'float',
        'wind_direction'  => 'float',
        'pancitemp'       => 'float',
        'pancilevel'      => 'float',
        'solrad'          => 'float',
        'watertemp'       => 'float',
        'waterlevel'      => 'float',
    ];

    protected $fillable = [
        'aws_id',
        'timestamp',
        'temperature',
        'humidity',
        'pressure',
        'rainfall',
        'wind_speed',
        'wind_direction',
        'pancitemp',
        'pancilevel',
        'solrad',
        'watertemp',
        'waterlevel',
    ];

    public $timestamps = true;

    public function aws()
    {
        return $this->belongsTo(Aws::class, 'aws_id');
    }
}
