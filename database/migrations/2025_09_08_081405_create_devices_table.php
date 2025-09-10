<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('nama_device');
            $table->string('serial_number')->unique();
            $table->foreignId('lembaga_id')->nullable()->constrained('lembagas');
            $table->enum('status', ['aktif', 'tidak_aktif', 'maintenance'])->default('tidak_aktif');
            $table->timestamp('last_connection')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};