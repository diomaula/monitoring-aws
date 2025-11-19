<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AwsSeeder::class,
            AwsLaporanSeeder::class, // jalankan dulu
            // DataAwsSeeder::class,    // menimpa data di jam tertentu (0,3,6,9,...)
            UserSeeder::class,
        ]);
    }
}