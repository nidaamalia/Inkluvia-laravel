<?php
// database/migrations/xxxx_create_materials_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('kategori'); // 'matematika', 'bahasa', 'ipa', dll
            $table->string('tingkat'); // 'sd', 'smp', 'sma', 'umum'
            $table->string('file_path')->nullable();
            $table->string('braille_data_path')->nullable();
            $table->integer('total_halaman')->default(0);
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft');
            $table->enum('akses', ['public', 'premium', 'restricted'])->default('public');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};