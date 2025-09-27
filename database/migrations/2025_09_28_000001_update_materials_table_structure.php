<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Hapus kolom yang tidak diperlukan
            $table->dropColumn(['file_path', 'braille_data_path', 'published_at']);
            
            // Ubah enum status
            $table->dropColumn('status');
            $table->enum('status', ['draft','processing','review','published','archived','pending'])->default('draft');
            
            // Ubah enum akses
            $table->dropColumn('akses');
            $table->enum('akses', ['public','premium','restricted','private'])->default('private');
            
            // Ubah foreign key constraint
            $table->dropForeign(['created_by']);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Kembalikan kolom yang dihapus
            $table->string('file_path')->nullable();
            $table->string('braille_data_path')->nullable();
            $table->timestamp('published_at')->nullable();
            
            // Kembalikan enum status
            $table->dropColumn('status');
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft');
            
            // Kembalikan enum akses
            $table->dropColumn('akses');
            $table->enum('akses', ['public', 'premium', 'restricted'])->default('public');
            
            // Kembalikan foreign key constraint
            $table->dropForeign(['created_by']);
            $table->foreign('created_by')->references('id')->on('users');
        });
    }
};
