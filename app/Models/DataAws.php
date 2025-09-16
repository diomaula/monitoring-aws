<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAws extends Model
{
    use HasFactory;

    protected $table = 'data_aws';

    protected $casts = [
        'timestamp' => 'datetime',
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


}
