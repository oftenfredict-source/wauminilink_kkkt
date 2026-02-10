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
        Schema::table('children', function (Blueprint $table) {
            // Drop the street column
            $table->dropColumn('street');
            
            // Add campus_id foreign key
            $table->foreignId('campus_id')->nullable()->after('is_church_member')->constrained('campuses')->onDelete('set null')->comment('Campus/Branch - only for member children');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            // Drop campus_id foreign key
            $table->dropForeign(['campus_id']);
            $table->dropColumn('campus_id');
            
            // Re-add street column
            $table->string('street')->nullable()->after('is_church_member');
        });
    }
};
