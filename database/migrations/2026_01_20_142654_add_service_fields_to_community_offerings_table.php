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
        Schema::table('community_offerings', function (Blueprint $table) {
            // Link to specific service (mid-week service)
            $table->foreignId('service_id')->nullable()->after('community_id')->constrained('sunday_services')->onDelete('set null');
            $table->string('service_type')->nullable()->after('service_id'); // prayer_meeting, bible_study, etc.
            
            // Collection method details
            $table->string('collection_method')->default('cash')->after('amount'); // cash, mobile_money, bank_transfer
            $table->string('reference_number')->nullable()->after('collection_method'); // For mobile money/bank transfers
            
            // Additional notes fields for each stage
            $table->text('elder_notes')->nullable()->after('notes');
            $table->text('leader_notes')->nullable()->after('elder_notes');
            $table->text('secretary_notes')->nullable()->after('leader_notes');
            $table->text('rejection_reason')->nullable()->after('secretary_notes');
            $table->foreignId('rejected_by')->nullable()->after('rejection_reason')->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            
            // Indexes for better query performance
            $table->index(['service_id', 'service_type']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('community_offerings', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['rejected_by']);
            $table->dropIndex(['service_id', 'service_type']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'service_id',
                'service_type',
                'collection_method',
                'reference_number',
                'elder_notes',
                'leader_notes',
                'secretary_notes',
                'rejection_reason',
                'rejected_by',
                'rejected_at'
            ]);
        });
    }
};
