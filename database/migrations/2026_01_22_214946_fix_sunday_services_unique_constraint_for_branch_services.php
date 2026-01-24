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
        // Drop the old unique constraint
        Schema::table('sunday_services', function (Blueprint $table) {
            try {
                $table->dropUnique('unique_service_date_type');
            } catch (\Exception $e) {
                // Constraint might not exist, continue
            }
        });

        // Use raw SQL to ensure the constraint is dropped
        DB::statement('ALTER TABLE `sunday_services` DROP INDEX IF EXISTS `unique_service_date_type`');

        // Add a generated column to handle NULL campus_id for main services
        // This allows main services (NULL campus_id) to be unique by date+type only
        // while branch services are unique by date+type+campus_id
        DB::statement('
            ALTER TABLE `sunday_services` 
            ADD COLUMN `unique_campus_id` BIGINT UNSIGNED GENERATED ALWAYS AS (
                CASE 
                    WHEN `is_branch_service` = 1 THEN `campus_id`
                    ELSE 0
                END
            ) STORED
        ');

        // Add unique constraint on service_date, service_type, unique_campus_id, is_branch_service
        Schema::table('sunday_services', function (Blueprint $table) {
            $table->unique(['service_date', 'service_type', 'unique_campus_id', 'is_branch_service'], 'unique_service_date_type_campus_branch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new unique constraint
        Schema::table('sunday_services', function (Blueprint $table) {
            try {
                $table->dropUnique('unique_service_date_type_campus_branch');
            } catch (\Exception $e) {
                // Constraint might not exist, continue
            }
        });

        // Drop the generated column
        DB::statement('ALTER TABLE `sunday_services` DROP COLUMN IF EXISTS `unique_campus_id`');

        // Restore the old unique constraint
        Schema::table('sunday_services', function (Blueprint $table) {
            $table->unique(['service_date', 'service_type'], 'unique_service_date_type');
        });
    }
};
