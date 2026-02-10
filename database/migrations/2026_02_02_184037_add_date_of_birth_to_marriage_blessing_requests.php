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
            $table->date('husband_date_of_birth')->after('husband_full_name')->nullable();
            $table->date('wife_date_of_birth')->after('wife_full_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marriage_blessing_requests', function (Blueprint $table) {
            $table->dropColumn(['husband_date_of_birth', 'wife_date_of_birth']);
        });
    }
};
