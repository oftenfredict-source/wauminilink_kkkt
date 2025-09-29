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
        Schema::create('sunday_services', function (Blueprint $table) {
            $table->id();
            $table->date('service_date');
            $table->string('theme')->nullable();
            $table->string('preacher')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('venue')->nullable();
            $table->unsignedInteger('attendance_count')->default(0);
            $table->decimal('offerings_amount', 12, 2)->default(0);
            $table->text('scripture_readings')->nullable();
            $table->string('choir')->nullable();
            $table->text('announcements')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique('service_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sunday_services');
    }
};


