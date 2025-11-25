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
        Schema::table('users', function (Blueprint $table) {
            // First, remove any duplicate phone numbers (set to null)
            // This ensures we don't have conflicts when adding the unique constraint
            DB::statement('UPDATE users u1 
                INNER JOIN (
                    SELECT phone_number, MIN(id) as min_id 
                    FROM users 
                    WHERE phone_number IS NOT NULL 
                    GROUP BY phone_number 
                    HAVING COUNT(*) > 1
                ) u2 ON u1.phone_number = u2.phone_number 
                SET u1.phone_number = NULL 
                WHERE u1.id != u2.min_id');
        });
        
        // Add unique constraint in a separate statement
        Schema::table('users', function (Blueprint $table) {
            $table->unique('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove unique constraint
            $table->dropUnique(['phone_number']);
            // Change back to regular nullable string
            $table->string('phone_number')->nullable()->change();
        });
    }
};

