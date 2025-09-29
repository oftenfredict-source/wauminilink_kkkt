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
            $table->string('member_id', 20)->nullable()->after('id');
        });
        
        // Generate member IDs for existing records
        $members = \App\Models\Member::whereNull('member_id')->get();
        foreach ($members as $member) {
            $member->member_id = \App\Models\Member::generateMemberId();
            $member->save();
        }
        
        // Now add the unique constraint
        Schema::table('members', function (Blueprint $table) {
            $table->string('member_id', 20)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('member_id');
        });
    }
};
