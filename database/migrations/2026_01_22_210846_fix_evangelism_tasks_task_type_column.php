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
        Schema::table('evangelism_tasks', function (Blueprint $table) {
            // Change task_type to string to accommodate longer values and avoid enum limitations
            // This allows values like 'member_visit', 'community_outreach', 'follow_up', 'other'
            $table->string('task_type', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We can't easily revert without knowing the exact original definition
        // but we can leave it as string which is safe
    }
};
