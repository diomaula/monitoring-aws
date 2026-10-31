<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aws extends Model
{
    use HasFactory;

    protected $table = 'aws';

    protected $fillable = [
        'name',
        'code',
        'location',
        'status',
    ];

    public function data()
    {
        return $this->hasMany(DataAws::class, 'aws_id');
    }

    public function laporanHarian()
    {
        return $this->hasMany(LaporanHarian::class, 'aws_id', 'id');
    }

     public function statusLogs()
    {
        return $this->hasMany(AwsStatusLog::class, 'aws_id');
    }
}
