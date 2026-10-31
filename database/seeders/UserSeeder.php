<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {

        DB::table('users')->insert([
            [
                'name' => 'Dio Maula',
                'username' => 'superadmin',
                'password' => Hash::make('12345'), 
                'role' => 'superadmin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Petugas Forecast',
                'username' => 'forecast',
                'password' => Hash::make('12345'),
                'role' => 'forecast',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Teknisi AWS',
                'username' => 'teknisi',
                'password' => Hash::make('12345'),
                'role' => 'teknisi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
