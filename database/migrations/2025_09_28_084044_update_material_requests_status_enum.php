<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, drop the existing foreign key constraints
        Schema::table('material_requests', function ($table) {
            // This will drop the foreign key constraint but keep the column
            $table->dropForeign(['requested_by']);
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['material_id']);
        });

        // Update the status column to use the correct enum values
        DB::statement("ALTER TABLE material_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");

        // Re-add the foreign key constraints with proper onDelete behavior
        Schema::table('material_requests', function ($table) {
            $table->foreign('requested_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('assigned_to')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('material_id')
                  ->references('id')
                  ->on('materials')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraints again
        Schema::table('material_requests', function ($table) {
            $table->dropForeign(['requested_by']);
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['material_id']);
        });

        // Revert the status column to the original enum values
        DB::statement("ALTER TABLE material_requests MODIFY COLUMN status ENUM('pending', 'diproses', 'selesai', 'ditolak') NOT NULL DEFAULT 'pending'");

        // Re-add the original foreign key constraints
        Schema::table('material_requests', function ($table) {
            $table->foreign('requested_by')
                  ->references('id')
                  ->on('users');
                  
            $table->foreign('assigned_to')
                  ->references('id')
                  ->on('users');
                  
            $table->foreign('material_id')
                  ->references('id')
                  ->on('materials');
        });
    }
};
