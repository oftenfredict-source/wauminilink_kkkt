<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'scheduled'
        DB::statement("ALTER TABLE `marriage_blessing_requests` MODIFY COLUMN `status` ENUM('pending', 'approved', 'counseling_required', 'rejected', 'completed', 'scheduled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum (but we can't easily remove enum values, so we'll keep it)
        DB::statement("ALTER TABLE `marriage_blessing_requests` MODIFY COLUMN `status` ENUM('pending', 'approved', 'counseling_required', 'rejected', 'completed') DEFAULT 'pending'");
    }
};








