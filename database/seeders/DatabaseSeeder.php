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
            BraillePatternsSeeder::class,
        ]);
        
        User::firstOrCreate(
            ['email' => 'admin@inkluvia.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'nama_lengkap' => 'Administrator Inkluvia',
                'tanggal_lahir' => '1990-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'lembaga_id' => 1, 
                'role' => 'admin'
            ]
        );

        User::firstOrCreate(
            ['email' => 'budisantoso@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('budisantoso'),
                'nama_lengkap' => 'Budi Santoso',
                'tanggal_lahir' => '1990-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'lembaga_id' => 1, 
                'role' => 'user'
            ]
        );

        User::firstOrCreate(
            ['email' => 'ahmadfauzan@gmail.com'],
            [
                'name' => 'Ahmad Fauzan',
                'password' => Hash::make('ahmadfauzan'),
                'nama_lengkap' => 'Ahmad Fauzan',
                'tanggal_lahir' => '2001-10-11',
                'jenis_kelamin' => 'Laki-laki',
                'lembaga_id' => 3, 
                'role' => 'user'
            ]
        );

        $this->call([
            EduBrailleSeeder::class,
            MaterialSeeder::class,
        ]);
    }
}