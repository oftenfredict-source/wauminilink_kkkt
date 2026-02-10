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
        Schema::table('members', function (Blueprint $table) {
            // Spouse welfare fields
            $table->enum('spouse_orphan_status', ['mother_deceased', 'father_deceased', 'both_deceased', 'not_orphan'])
                  ->default('not_orphan')
                  ->after('spouse_other_tribe');
            $table->boolean('spouse_disability_status')->default(false)->after('spouse_orphan_status');
            $table->string('spouse_disability_type')->nullable()->after('spouse_disability_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['spouse_orphan_status', 'spouse_disability_status', 'spouse_disability_type']);
        });
    }
};
