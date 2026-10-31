<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwsStatusLog extends Model
{
    use HasFactory;

    protected $table = 'aws_status_log';

    protected $fillable = [
        'aws_id',
        'status',
        'waktu'
    ];

    public $timestamps = true;

    public function aws()
    {
        return $this->belongsTo(Aws::class, 'aws_id');
    }
}
