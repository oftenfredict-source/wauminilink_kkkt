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
        Schema::create('church_elder_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('church_elder_id'); // User ID of the church elder
            $table->unsignedBigInteger('community_id'); // Community this task is for
            $table->unsignedBigInteger('member_id')->nullable(); // Member related to task (if applicable)
            $table->string('task_type'); // member_visit, prayer_request, follow_up, outreach, other
            $table->string('task_title');
            $table->text('description');
            $table->date('task_date');
            $table->time('task_time')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->text('outcome')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('church_elder_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('church_elder_tasks');
    }
};
