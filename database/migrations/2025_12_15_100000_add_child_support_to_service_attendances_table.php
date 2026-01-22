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
        // Check if child_id column already exists
        $columnExists = Schema::hasColumn('service_attendances', 'child_id');
        
        // Check if constraints exist
        $memberConstraintExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.table_constraints 
            WHERE table_schema = DATABASE() 
            AND table_name = 'service_attendances' 
            AND constraint_name = 'unique_member_service_attendance'
        ");
        
        $childConstraintExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.table_constraints 
            WHERE table_schema = DATABASE() 
            AND table_name = 'service_attendances' 
            AND constraint_name = 'unique_child_service_attendance'
        ");
        
        // Drop old unique constraint if it exists
        if ($memberConstraintExists[0]->count > 0) {
            DB::statement('ALTER TABLE `service_attendances` DROP INDEX `unique_member_service_attendance`');
        }
        
        Schema::table('service_attendances', function (Blueprint $table) use ($columnExists) {
            // Make member_id nullable to support children attendance
            if (Schema::hasColumn('service_attendances', 'member_id')) {
                $table->unsignedBigInteger('member_id')->nullable()->change();
            }
            
            // Add child_id column for children attendance only if it doesn't exist
            if (!$columnExists) {
                $table->unsignedBigInteger('child_id')->nullable()->after('member_id');
            }
        });
        
        // Add foreign key constraint and index for child_id only if column was just added
        if (!$columnExists) {
            Schema::table('service_attendances', function (Blueprint $table) {
                // Add foreign key constraint for child_id
                $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');
                
                // Add index for child_id
                $table->index('child_id');
            });
        }
        
        // Add new unique constraints only if they don't exist
        // Note: In MySQL, NULL values are not considered equal in unique constraints,
        // so we can have multiple rows with NULL member_id or NULL child_id
        // We'll enforce the "either member_id OR child_id" rule at the application level
        if ($memberConstraintExists[0]->count == 0) {
            Schema::table('service_attendances', function (Blueprint $table) {
                // For member attendance: unique on service_type, service_id, member_id
                // This allows multiple NULL member_ids but prevents duplicate non-null member_ids
                $table->unique(['service_type', 'service_id', 'member_id'], 'unique_member_service_attendance');
            });
        }
        
        if ($childConstraintExists[0]->count == 0) {
            Schema::table('service_attendances', function (Blueprint $table) {
                // For child attendance: unique on service_type, service_id, child_id
                // This allows multiple NULL child_ids but prevents duplicate non-null child_ids
                $table->unique(['service_type', 'service_id', 'child_id'], 'unique_child_service_attendance');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_attendances', function (Blueprint $table) {
            // Drop the new unique constraints
            $table->dropUnique('unique_child_service_attendance');
            $table->dropUnique('unique_member_service_attendance');
            
            // Drop foreign key and index for child_id
            $table->dropForeign(['child_id']);
            $table->dropIndex(['child_id']);
            
            // Remove child_id column
            $table->dropColumn('child_id');
        });
        
        // Make member_id required again and restore the original unique constraint
        Schema::table('service_attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('member_id')->nullable(false)->change();
            $table->unique(['service_type', 'service_id', 'member_id'], 'unique_member_service_attendance');
        });
    }
};

