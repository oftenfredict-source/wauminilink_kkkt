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
        Schema::dropIfExists('church_wedding_requests'); // Drop if exists to recreate
        
        Schema::create('church_wedding_requests', function (Blueprint $table) {
            $table->id();
            
            // Bride & Groom Information
            $table->string('groom_full_name');
            $table->date('groom_date_of_birth')->nullable();
            $table->string('groom_phone_number')->nullable();
            $table->string('groom_email')->nullable();
            
            $table->string('bride_full_name');
            $table->date('bride_date_of_birth')->nullable();
            $table->string('bride_phone_number')->nullable();
            $table->string('bride_email')->nullable();
            
            $table->foreignId('church_branch_id')->nullable()->constrained('campuses')->onDelete('set null');
            
            // Membership & Spiritual Information
            $table->boolean('both_baptized')->default(false);
            $table->boolean('both_confirmed')->default(false);
            $table->string('membership_duration')->nullable(); // e.g., "1 year", "3 years"
            $table->string('pastor_catechist_name')->nullable();
            
            // Wedding Details
            $table->date('preferred_wedding_date');
            $table->string('preferred_church')->nullable();
            $table->integer('expected_guests')->nullable();
            
            // Counseling & Documents
            $table->boolean('attended_premarital_counseling')->default(false);
            
            // Document paths
            $table->string('groom_baptism_certificate_path')->nullable();
            $table->string('bride_baptism_certificate_path')->nullable();
            $table->string('groom_confirmation_certificate_path')->nullable();
            $table->string('bride_confirmation_certificate_path')->nullable();
            $table->string('groom_birth_certificate_path')->nullable();
            $table->string('bride_birth_certificate_path')->nullable();
            $table->string('marriage_notice_path')->nullable(); // Civil certificate if required
            
            // Declaration
            $table->boolean('declaration_agreed')->default(false);
            
            // Workflow
            $table->foreignId('evangelism_leader_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pastor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'documents_required', 'rejected', 'scheduled', 'completed'])->default('pending');
            $table->text('pastor_comments')->nullable();
            $table->date('wedding_approval_date')->nullable();
            $table->date('confirmed_wedding_date')->nullable();
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
        Schema::dropIfExists('church_wedding_requests');
    }
};
