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
            $table->string('guardian_phone')->nullable()->after('parent_guardian_name');
            $table->string('guardian_email')->nullable()->after('guardian_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baptism_applications', function (Blueprint $table) {
            $table->dropColumn(['guardian_phone', 'guardian_email']);
        });
    }
};
