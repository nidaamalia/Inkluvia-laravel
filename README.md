# Inkluvia

## Prerequisites

Pastikan sudah memiliki:
- PHP (versi 8.0 atau lebih tinggi)
- Composer
- XAMPP (atau server lokal lainnya)
- MySQL

## Instalasi & Setup

Ikuti langkah-langkah berikut untuk menjalankan file:

### 1. Clon atau Fork Repository
```bash
git clone https://github.com/Aliyah-Pattoza/Inkluvia.git
```

### 2. Pindahkan ke Folder XAMPP
Salin folder project ke dalam direktori `xampp/htdocs`

### 3. Install Dependencies
```bash
composer install
composer require --dev barryvdh/laravel-ide-helper
```

### 4. Generate IDE Helper
```bash
php artisan ide-helper:generate
```

### 5. Konfigurasi Database
- Salin file `.env.example` menjadi `.env`
- Sesuaikan konfigurasi database pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inkluvia
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Setup Database
- Buat database baru bernama `inkluvia` di MySQL
- Jalankan migrasi dan seeder:
```bash
php artisan migrate
php artisan db:seed --class=DatabaseSeeder
php artisan db:seed

```

python -m pip install pymupdf
py -m pip install google-genai pillow pymupdf

### 7. Jalankan Server
- Aktifkan MySQL pada XAMPP Control Panel
- Start server Laravel:
```bash
php artisan serve
```

### 8. Akses Aplikasi
Buka browser dan akses: `http://localhost:8000`
