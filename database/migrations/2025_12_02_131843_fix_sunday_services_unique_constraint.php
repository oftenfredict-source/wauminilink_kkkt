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
        // First, check if the old unique constraint exists and drop it
        Schema::table('sunday_services', function (Blueprint $table) {
            // Try to drop the old unique constraint if it exists
            // We'll use raw SQL to handle this safely
        });
        
        // Use raw SQL to drop the constraint if it exists
        // MySQL constraint names are usually the column name
        DB::statement('ALTER TABLE `sunday_services` DROP INDEX IF EXISTS `sunday_services_service_date_unique`');
        
        // Now add the new composite unique constraint
        Schema::table('sunday_services', function (Blueprint $table) {
            // Add new unique constraint on service_date + service_type combination
            // This allows multiple service types on the same date
            $table->unique(['service_date', 'service_type'], 'unique_service_date_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sunday_services', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('unique_service_date_type');
            
            // Restore the old unique constraint on service_date only
            $table->unique('service_date');
        });
    }
};
