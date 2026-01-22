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
        Schema::dropIfExists('marriage_blessing_requests'); // Drop if exists to recreate
        
        Schema::create('marriage_blessing_requests', function (Blueprint $table) {
            $table->id();
            
            // Couple Information
            $table->string('husband_full_name');
            $table->string('wife_full_name');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('church_branch_id')->nullable()->constrained('campuses')->onDelete('set null');
            
            // Marriage Details
            $table->enum('marriage_type', ['customary', 'civil', 'traditional', 'other'])->nullable();
            $table->date('marriage_date');
            $table->string('place_of_marriage')->nullable();
            $table->string('marriage_certificate_number')->nullable();
            
            // Church Information
            $table->boolean('both_spouses_members')->default(false);
            $table->string('membership_duration')->nullable(); // e.g., "2 years", "5 years"
            $table->boolean('attended_marriage_counseling')->default(false);
            
            // Declaration
            $table->text('reason_for_blessing');
            $table->boolean('declaration_agreed')->default(false);
            
            // Workflow
            $table->foreignId('evangelism_leader_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pastor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'counseling_required', 'rejected', 'completed'])->default('pending');
            $table->text('pastor_comments')->nullable();
            $table->date('scheduled_blessing_date')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marriage_blessing_requests');
    }
};
