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
            $table->enum('orphan_status', ['mother_deceased', 'father_deceased', 'both_deceased', 'not_orphan'])->default('not_orphan')->after('lives_outside_main_area');
            $table->boolean('disability_status')->default(false)->after('orphan_status');
            $table->string('disability_type')->nullable()->after('disability_status');
            $table->boolean('vulnerable_status')->default(false)->after('disability_type');
            $table->string('vulnerable_type')->nullable()->after('vulnerable_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn(['orphan_status', 'disability_status', 'disability_type', 'vulnerable_status', 'vulnerable_type']);
        });
    }
};
