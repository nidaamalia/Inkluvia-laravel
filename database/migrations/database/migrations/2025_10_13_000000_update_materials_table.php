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
        Schema::table('materials', function (Blueprint $table) {
            // Tambah kolom kelas
            $table->string('kelas')->nullable()->after('tingkat');
            
            // Hapus kolom yang tidak diperlukan
            $table->dropColumn([
                'tahun_terbit',
                'penerbit',
                'edisi'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Kembalikan kolom yang dihapus
            $table->integer('tahun_terbit')->nullable();
            $table->string('penerbit')->nullable();
            $table->string('edisi')->nullable();
            
            // Hapus kolom kelas
            $table->dropColumn('kelas');
        });
    }
};