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
        Schema::table('sunday_services', function (Blueprint $table) {
            $table->foreignId('campus_id')->nullable()->after('church_elder_id')->constrained('campuses')->onDelete('set null');
            $table->foreignId('evangelism_leader_id')->nullable()->after('campus_id')->constrained('users')->onDelete('set null');
            $table->boolean('is_branch_service')->default(false)->after('evangelism_leader_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sunday_services', function (Blueprint $table) {
            $table->dropForeign(['campus_id']);
            $table->dropForeign(['evangelism_leader_id']);
            $table->dropColumn(['campus_id', 'evangelism_leader_id', 'is_branch_service']);
        });
    }
};
