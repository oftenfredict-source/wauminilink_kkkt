<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('member_type')->nullable()->after('member_id');
            $table->string('membership_type')->nullable()->after('member_type');
            $table->string('education_level')->nullable()->after('gender');
            $table->string('profession')->nullable()->after('education_level');
            $table->string('guardian_name')->nullable()->after('profession');
            $table->string('guardian_phone')->nullable()->after('guardian_name');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['member_type','membership_type','education_level','profession','guardian_name','guardian_phone']);
        });
    }
};



