<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwsStatusLog extends Model
{
    protected $table = 'aws_status_log';
    protected $fillable = [
        'station_id', 'name', 'status', 'waktu'
    ];

    public $timestamps = true;
}