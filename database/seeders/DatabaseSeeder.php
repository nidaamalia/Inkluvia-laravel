<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LembagaSeeder::class,
        ]);

        // Create default admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@inkluvia.com',
            'password' => Hash::make('admin123'),
            'nama_lengkap' => 'Administrator Inkluvia',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'Laki-laki',
            'lembaga_id' => 1, 
            'role' => 'admin'
        ]);
    }
}