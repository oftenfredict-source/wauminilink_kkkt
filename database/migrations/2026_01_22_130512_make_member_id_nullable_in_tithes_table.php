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
        Schema::table('tithes', function (Blueprint $table) {
            // Make member_id nullable to support aggregate tithes
            $table->foreignId('member_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tithes', function (Blueprint $table) {
            // Remove nullable tithes first (set to a default member if needed)
            // Then make it required again
            $table->foreignId('member_id')->nullable(false)->change();
        });
    }
};
