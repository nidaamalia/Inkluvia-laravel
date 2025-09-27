<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->integer('page_number');
            $table->json('lines'); // Menyimpan array data braille untuk setiap baris
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['material_id', 'page_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_pages');
    }
};
