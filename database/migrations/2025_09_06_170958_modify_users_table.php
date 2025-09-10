<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_lengkap');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->foreignId('lembaga_id')->nullable()->constrained('lembagas');
            $table->enum('role', ['user', 'admin'])->default('user');

            $table->index(['role', 'lembaga_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['lembaga_id']);
            $table->dropColumn(['nama_lengkap', 'tanggal_lahir', 'jenis_kelamin', 'lembaga_id', 'role']);
        });
    }
};