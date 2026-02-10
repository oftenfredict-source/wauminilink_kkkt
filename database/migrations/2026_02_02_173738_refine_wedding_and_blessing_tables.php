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
        Schema::table('marriage_blessing_requests', function (Blueprint $table) {
            $table->date('marriage_date')->nullable()->change();
            $table->dateTime('scheduled_meeting_date')->after('status')->nullable();
        });

        Schema::table('church_wedding_requests', function (Blueprint $table) {
            $table->date('preferred_wedding_date')->nullable()->change();
            $table->dateTime('scheduled_meeting_date')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marriage_blessing_requests', function (Blueprint $table) {
            $table->date('marriage_date')->nullable(false)->change();
            $table->dropColumn('scheduled_meeting_date');
        });

        Schema::table('church_wedding_requests', function (Blueprint $table) {
            $table->date('preferred_wedding_date')->nullable(false)->change();
            $table->dropColumn('scheduled_meeting_date');
        });
    }
};
