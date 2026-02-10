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
        try {
            Schema::table('department_member', function (Blueprint $table) {
                if (Schema::hasColumn('department_member', 'member_id')) {
                    $table->unsignedBigInteger('member_id')->nullable()->change();
                }
                
                if (!Schema::hasColumn('department_member', 'child_id')) {
                    $table->foreignId('child_id')->after('member_id')->nullable()->constrained('children')->onDelete('cascade');
                }
            });
        } catch (\Exception $e) {
            // Probably column already exists or similar error, ignore
        }

        try {
            // Handle indexes safely using native Schema methods if available (Laravel 11+)
            $indexes = Schema::getIndexes('department_member');
            $indexNames = array_column($indexes, 'name');

            Schema::table('department_member', function (Blueprint $table) use ($indexNames) {
                if (in_array('department_member_department_id_member_id_unique', $indexNames)) {
                    $table->dropUnique('department_member_department_id_member_id_unique');
                }
                
                if (!in_array('dept_member_child_unique', $indexNames)) {
                    $table->unique(['department_id', 'member_id', 'child_id'], 'dept_member_child_unique');
                }
            });
        } catch (\Exception $e) {
            // Index might already exist or been dropped, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_member', function (Blueprint $table) {
            $table->dropUnique('dept_member_child_unique');
            $table->unique(['department_id', 'member_id']);
            
            $table->dropForeign(['child_id']);
            $table->dropColumn('child_id');
            $table->unsignedBigInteger('member_id')->nullable(false)->change();
        });
    }
};
