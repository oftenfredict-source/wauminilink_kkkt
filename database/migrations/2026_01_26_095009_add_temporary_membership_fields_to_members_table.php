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
        Schema::table('members', function (Blueprint $table) {
            // Temporary membership fields
            $table->integer('membership_duration_months')->nullable()->after('membership_type')->comment('Duration in months for temporary membership');
            $table->date('membership_start_date')->nullable()->after('membership_duration_months')->comment('Start date of temporary membership');
            $table->date('membership_end_date')->nullable()->after('membership_start_date')->comment('End date of temporary membership');
            $table->string('membership_status')->default('active')->after('membership_end_date')->comment('active, expired, extended, converted, completed');
            
            // Index for efficient querying of expiring memberships
            $table->index(['membership_type', 'membership_end_date']);
            $table->index(['membership_status', 'membership_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['membership_type', 'membership_end_date']);
            $table->dropIndex(['membership_status', 'membership_end_date']);
            $table->dropColumn([
                'membership_duration_months',
                'membership_start_date',
                'membership_end_date',
                'membership_status'
            ]);
        });
    }
};
