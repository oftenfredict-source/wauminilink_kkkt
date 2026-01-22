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
        Schema::table('baptism_applications', function (Blueprint $table) {
            // Drop zone and diocese columns
            $table->dropColumn(['zone', 'diocese']);
            
            // Add community_id column
            $table->foreignId('community_id')->nullable()->after('church_branch_id')->constrained('communities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baptism_applications', function (Blueprint $table) {
            // Drop community_id
            $table->dropForeign(['community_id']);
            $table->dropColumn('community_id');
            
            // Add back zone and diocese
            $table->string('zone')->nullable()->after('church_branch_id');
            $table->string('diocese')->nullable()->after('zone');
        });
    }
};
