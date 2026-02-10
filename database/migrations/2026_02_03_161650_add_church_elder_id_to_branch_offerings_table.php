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
        Schema::table('branch_offerings', function (Blueprint $table) {
            $table->foreignId('church_elder_id')->nullable()->after('campus_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_offerings', function (Blueprint $table) {
            $table->dropForeign(['church_elder_id']);
            $table->dropColumn('church_elder_id');
        });
    }
};
