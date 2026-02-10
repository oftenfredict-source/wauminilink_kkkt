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
            $table->foreignId('spouse_campus_id')->nullable()->after('spouse_member_id')->constrained('campuses')->onDelete('set null')->comment('Spouse campus if spouse is a church member');
            $table->foreignId('spouse_community_id')->nullable()->after('spouse_campus_id')->constrained('communities')->onDelete('set null')->comment('Spouse community/fellowship if spouse is a church member');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['spouse_campus_id']);
            $table->dropForeign(['spouse_community_id']);
            $table->dropColumn(['spouse_campus_id', 'spouse_community_id']);
        });
    }
};
