<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('braille_patterns', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // latin, angka, hijaiyah, harakat, simbol
            $table->string('character'); // huruf/angka/simbol asli, case sensitive
            $table->string('braille_unicode')->charset('utf8mb4');
            $table->string('dots_binary'); // misal "100000"
            $table->integer('dots_decimal'); // konversi dari binary
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('braille_patterns');
    }
};
