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
        Schema::table('deleted_members', function (Blueprint $table) {
            $table->unsignedBigInteger('campus_id')->nullable()->after('member_id');
            $table->index('campus_id');
        });
        
        // Backfill campus_id from member_snapshot for existing records
        \DB::statement("
            UPDATE deleted_members 
            SET campus_id = CAST(JSON_EXTRACT(member_snapshot, '$.campus_id') AS UNSIGNED)
            WHERE campus_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deleted_members', function (Blueprint $table) {
            $table->dropIndex(['campus_id']);
            $table->dropColumn('campus_id');
        });
    }
};
