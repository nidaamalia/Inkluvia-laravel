<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Sesuaikan enum status tanpa menghapus kolom
            DB::statement("ALTER TABLE materials MODIFY COLUMN status ENUM('draft','processing','review','published','archived','pending') DEFAULT 'draft'");

            // Sesuaikan enum akses tanpa menghapus kolom
            DB::statement("ALTER TABLE materials MODIFY COLUMN akses ENUM('public','premium','restricted','private') DEFAULT 'private'");

            // Ubah foreign key constraint
            $table->dropForeign(['created_by']);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Kembalikan enum status ke nilai sebelumnya tanpa menghapus kolom
            DB::statement("ALTER TABLE materials MODIFY COLUMN status ENUM('draft','review','published','archived') DEFAULT 'draft'");

            // Kembalikan enum akses ke nilai sebelumnya tanpa menghapus kolom
            DB::statement("ALTER TABLE materials MODIFY COLUMN akses ENUM('public','premium','restricted') DEFAULT 'public'");

            // Kembalikan foreign key constraint
            $table->dropForeign(['created_by']);
            $table->foreign('created_by')->references('id')->on('users');
        });
    }
};
