<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aws extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'aws';

    // Kolom yang boleh diisi
    protected $fillable = [
        'name',
        'code',
        'location',
        'status',
    ];

    // Relasi ke data (misalnya DataAws)
    public function data()
    {
        return $this->hasMany(DataAws::class, 'aws_id');
    }
}
