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
        // Add fields to offerings table
        Schema::table('offerings', function (Blueprint $table) {
            if (!Schema::hasColumn('offerings', 'campus_id')) {
                $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('set null')->after('member_id');
            }
            if (!Schema::hasColumn('offerings', 'evangelism_leader_id')) {
                $table->foreignId('evangelism_leader_id')->nullable()->constrained('users')->onDelete('set null')->after('campus_id');
            }
            if (!Schema::hasColumn('offerings', 'submitted_to_secretary')) {
                $table->boolean('submitted_to_secretary')->default(false)->after('evangelism_leader_id');
            }
            if (!Schema::hasColumn('offerings', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('submitted_to_secretary');
            }
        });

        // Add fields to tithes table
        Schema::table('tithes', function (Blueprint $table) {
            if (!Schema::hasColumn('tithes', 'campus_id')) {
                $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('set null')->after('member_id');
            }
            if (!Schema::hasColumn('tithes', 'evangelism_leader_id')) {
                $table->foreignId('evangelism_leader_id')->nullable()->constrained('users')->onDelete('set null')->after('campus_id');
            }
            if (!Schema::hasColumn('tithes', 'is_aggregate')) {
                $table->boolean('is_aggregate')->default(false)->after('evangelism_leader_id');
            }
            if (!Schema::hasColumn('tithes', 'submitted_to_secretary')) {
                $table->boolean('submitted_to_secretary')->default(false)->after('is_aggregate');
            }
            if (!Schema::hasColumn('tithes', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('submitted_to_secretary');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offerings', function (Blueprint $table) {
            if (Schema::hasColumn('offerings', 'submitted_at')) {
                $table->dropColumn('submitted_at');
            }
            if (Schema::hasColumn('offerings', 'submitted_to_secretary')) {
                $table->dropColumn('submitted_to_secretary');
            }
            if (Schema::hasColumn('offerings', 'evangelism_leader_id')) {
                $table->dropForeign(['evangelism_leader_id']);
                $table->dropColumn('evangelism_leader_id');
            }
            if (Schema::hasColumn('offerings', 'campus_id')) {
                $table->dropForeign(['campus_id']);
                $table->dropColumn('campus_id');
            }
        });

        Schema::table('tithes', function (Blueprint $table) {
            if (Schema::hasColumn('tithes', 'submitted_at')) {
                $table->dropColumn('submitted_at');
            }
            if (Schema::hasColumn('tithes', 'submitted_to_secretary')) {
                $table->dropColumn('submitted_to_secretary');
            }
            if (Schema::hasColumn('tithes', 'is_aggregate')) {
                $table->dropColumn('is_aggregate');
            }
            if (Schema::hasColumn('tithes', 'evangelism_leader_id')) {
                $table->dropForeign(['evangelism_leader_id']);
                $table->dropColumn('evangelism_leader_id');
            }
            if (Schema::hasColumn('tithes', 'campus_id')) {
                $table->dropForeign(['campus_id']);
                $table->dropColumn('campus_id');
            }
        });
    }
};
