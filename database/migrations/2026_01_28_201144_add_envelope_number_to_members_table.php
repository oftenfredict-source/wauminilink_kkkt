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
            $table->string('envelope_number')->nullable()->after('community_id');
            // Unique within the same Jumuiya (community)
            $table->unique(['community_id', 'envelope_number'], 'members_community_envelope_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropUnique('members_community_envelope_unique');
            $table->dropColumn('envelope_number');
        });
    }
};
