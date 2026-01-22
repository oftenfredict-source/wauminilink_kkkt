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
        Schema::create('branch_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained('campuses')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('sunday_services')->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->date('offering_date');
            $table->string('collection_method')->default('cash'); // cash, mobile_money, bank_transfer
            $table->string('reference_number')->nullable();
            
            // Workflow tracking
            $table->foreignId('evangelism_leader_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('secretary_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Status: pending_secretary (Leader has created, waiting for Secretary), completed (Secretary has confirmed)
            $table->string('status')->default('pending_secretary');
            
            $table->timestamp('handover_to_secretary_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('leader_notes')->nullable();
            $table->text('secretary_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_offerings');
    }
};
