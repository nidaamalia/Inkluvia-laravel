<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // Tambahan kolom 'kelas'
            $table->string('kelas')->nullable()->after('tingkat');

            // Kolom yang sudah ada sebelumnya
            $table->timestamp('processing_started_at')->nullable()->after('status');
            $table->timestamp('processing_completed_at')->nullable()->after('processing_started_at');
            $table->integer('processing_time')->nullable()->comment('seconds')->after('processing_completed_at');
            $table->text('processing_error')->nullable()->after('processing_time');
            $table->json('ai_stats')->nullable()->after('processing_error');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn([
                'kelas',
                'processing_started_at',
                'processing_completed_at',
                'processing_time',
                'processing_error',
                'ai_stats'
            ]);
        });
    }
};