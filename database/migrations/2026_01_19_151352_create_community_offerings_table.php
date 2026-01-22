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
        Schema::create('community_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained('communities')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->date('offering_date');
            
            // Workflow tracking
            $table->foreignId('church_elder_id')->constrained('users')->onDelete('cascade'); // The elder who collected/recorded it
            $table->foreignId('evangelism_leader_id')->nullable()->constrained('users')->onDelete('set null'); // The leader who received it
            $table->foreignId('secretary_id')->nullable()->constrained('users')->onDelete('set null'); // The secretary who finalized it
            
            // Status: pending_evangelism (Elder has created, waiting for Leader), 
            //         pending_secretary (Leader has confirmed, waiting for Secretary), 
            //         completed (Secretary has confirmed)
            $table->string('status')->default('pending_evangelism');
            
            $table->timestamp('handover_to_evangelism_at')->nullable();
            $table->timestamp('handover_to_secretary_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_offerings');
    }
};
