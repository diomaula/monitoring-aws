<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AwsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('aws')->insert([
            [
                'id'         => 1,
                'name'       => 'AWS Digi Banyuwangi',
                'code'       => '5000000031',
                'location'   => 'Banyuwangi',
                'status'     => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 2,
                'name'       => 'AWS Maritim Ketapang',
                'code'       => '3000000007',
                'location'   => 'Ketapang',
                'status'     => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 3,
                'name'       => 'AWS Maritim Gilimanuk',
                'code'       => '3000000046',
                'location'   => 'Gilimanuk',
                'status'     => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
