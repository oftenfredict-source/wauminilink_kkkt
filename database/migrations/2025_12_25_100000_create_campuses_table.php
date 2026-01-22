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
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Unique campus code (e.g., MAIN, SUB-001)
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('campuses')->onDelete('cascade'); // For sub campuses
            $table->boolean('is_main_campus')->default(false); // Flag to identify main campus
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campuses');
    }
};














