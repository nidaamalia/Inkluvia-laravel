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

---

## Deployment ke Railway

### Prerequisites
- Akun Railway (https://railway.app)
- Repository GitHub yang sudah terhubung

### Langkah-langkah Deployment

#### 1. Setup Project di Railway
- Login ke Railway
- Klik "New Project" → "Deploy from GitHub repo"
- Pilih repository Inkluvia

#### 2. Tambahkan Database
- Di dashboard Railway, klik "New" → "Database" → "Add PostgreSQL" atau "Add MySQL"
- Railway akan otomatis membuat environment variables untuk database

#### 3. Konfigurasi Environment Variables
Tambahkan environment variables berikut di Railway:

```env
APP_NAME=Inkluvia
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app

DB_CONNECTION=mysql
# Database credentials akan otomatis terisi oleh Railway

CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_LEVEL=error
```

**Penting:** Generate `APP_KEY` dengan menjalankan:
```bash
php artisan key:generate --show
```

#### 4. Deploy
- Railway akan otomatis mendeteksi `nixpacks.toml` dan `Procfile`
- Build dan deployment akan berjalan otomatis
- Migrations akan dijalankan otomatis saat deployment

#### 5. Verifikasi
- Akses URL yang diberikan Railway
- Pastikan aplikasi berjalan dengan baik

### Troubleshooting

**Error: "no such table: cache"**
- Pastikan migration sudah dijalankan
- Cek environment variable `CACHE_STORE=database`
- Restart deployment di Railway

**Error: Database connection**
- Pastikan database Railway sudah aktif
- Cek environment variables database sudah benar
- Tunggu beberapa menit untuk database siap

**Error: 500 Internal Server Error**
- Set `APP_DEBUG=true` sementara untuk melihat error detail
- Cek logs di Railway dashboard
- Pastikan `APP_KEY` sudah di-set
