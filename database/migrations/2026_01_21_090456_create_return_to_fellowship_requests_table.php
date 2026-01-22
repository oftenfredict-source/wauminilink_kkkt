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
        Schema::dropIfExists('return_to_fellowship_requests'); // Drop if exists to recreate
        
        Schema::create('return_to_fellowship_requests', function (Blueprint $table) {
            $table->id();
            
            // Personal Information
            $table->string('full_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('church_branch_id')->nullable()->constrained('campuses')->onDelete('set null');
            
            // Church Background
            $table->boolean('previously_member')->default(false);
            $table->string('previous_church_branch')->nullable();
            $table->string('period_away')->nullable(); // e.g., "2 years", "6 months"
            $table->text('reason_for_leaving')->nullable();
            
            // Return Declaration
            $table->text('reason_for_returning');
            $table->boolean('declaration_agreed')->default(false);
            
            // Workflow
            $table->foreignId('evangelism_leader_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pastor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'counseling_required', 'rejected', 'completed'])->default('pending');
            $table->text('pastor_comments')->nullable();
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
        Schema::dropIfExists('return_to_fellowship_requests');
    }
};
