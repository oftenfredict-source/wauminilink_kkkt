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
        Schema::table('sunday_services', function (Blueprint $table) {
            // Drop the old unique constraint on service_date only
            $table->dropUnique(['service_date']);
            
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
