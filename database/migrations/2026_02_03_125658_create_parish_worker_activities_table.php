<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parish_worker_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('campus_id')->constrained()->onDelete('cascade');
            $table->enum('activity_type', [
                'altar_cleanliness',
                'womens_department',
                'sunday_school',
                'holy_communion',
                'church_candles',
                'other'
            ]);
            $table->string('title');
            $table->text('description');
            $table->date('activity_date');
            $table->enum('status', ['completed', 'pending'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parish_worker_activities');
    }
};
