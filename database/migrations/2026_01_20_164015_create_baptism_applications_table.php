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
        Schema::create('baptism_applications', function (Blueprint $table) {
            $table->id();
            
            // Personal Information
            $table->string('full_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->integer('age');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->text('residential_address');
            $table->foreignId('church_branch_id')->nullable()->constrained('campuses')->onDelete('set null');
            $table->string('zone')->nullable();
            $table->string('diocese')->nullable();
            
            // Spiritual Information
            $table->boolean('previously_baptized')->default(false);
            $table->string('previous_church_name')->nullable();
            $table->date('previous_baptism_date')->nullable();
            $table->boolean('attended_baptism_classes')->default(false);
            $table->string('church_attendance_duration')->nullable(); // e.g., "1 year", "2 years"
            $table->string('pastor_catechist_name')->nullable();
            
            // Family Information
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('parent_guardian_name')->nullable();
            $table->text('family_religious_background')->nullable();
            
            // Application Statement
            $table->text('reason_for_baptism');
            $table->boolean('declaration_agreed')->default(false);
            
            // Attachments (file paths)
            $table->string('photo_path')->nullable();
            $table->string('recommendation_letter_path')->nullable();
            
            // Workflow
            $table->foreignId('evangelism_leader_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pastor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected', 'scheduled', 'completed'])->default('pending');
            $table->text('pastor_comments')->nullable();
            $table->date('scheduled_baptism_date')->nullable();
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
        Schema::dropIfExists('baptism_applications');
    }
};
