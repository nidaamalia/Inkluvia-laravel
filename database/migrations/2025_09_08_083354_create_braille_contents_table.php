<?php
// database/migrations/xxxx_create_braille_contents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('braille_contents', function (Blueprint $table) {
            $table->id();
            $table->string('karakter');
            $table->string('braille_pattern', 6); // 6-digit binary pattern
            $table->string('kategori'); // 'huruf', 'angka', 'operator', 'hijaiyah', dll
            $table->string('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('braille_contents');
    }
};