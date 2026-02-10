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
        Schema::table('baptism_applications', function (Blueprint $table) {
            $table->string('father_name')->nullable()->after('marital_status');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('place_of_birth')->nullable()->after('date_of_birth');
            $table->string('godparent1_name')->nullable()->after('family_religious_background');
            $table->string('godparent2_name')->nullable()->after('godparent1_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baptism_applications', function (Blueprint $table) {
            $table->dropColumn(['father_name', 'mother_name', 'place_of_birth', 'godparent1_name', 'godparent2_name']);
        });
    }
};
