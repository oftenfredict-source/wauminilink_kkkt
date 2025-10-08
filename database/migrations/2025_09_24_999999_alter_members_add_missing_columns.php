<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (!Schema::hasColumn('members', 'member_type')) {
                $table->string('member_type')->nullable()->after('member_id');
            }
            if (!Schema::hasColumn('members', 'membership_type')) {
                $table->string('membership_type')->nullable()->after('member_type');
            }
            if (!Schema::hasColumn('members', 'education_level')) {
                $table->string('education_level')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('members', 'profession')) {
                $table->string('profession')->nullable()->after('education_level');
            }
            if (!Schema::hasColumn('members', 'guardian_name')) {
                $table->string('guardian_name')->nullable()->after('profession');
            }
            if (!Schema::hasColumn('members', 'guardian_phone')) {
                $table->string('guardian_phone')->nullable()->after('guardian_name');
            }
            if (!Schema::hasColumn('members', 'guardian_relationship')) {
                $table->string('guardian_relationship')->nullable()->after('guardian_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // No-op: intentionally not dropping columns in down for safety
        });
    }
};



