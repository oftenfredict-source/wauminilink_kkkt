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
            $table->enum('spouse_alive', ['yes', 'no'])->nullable()->after('profile_picture');
            $table->string('spouse_full_name')->nullable()->after('spouse_alive');
            $table->date('spouse_date_of_birth')->nullable()->after('spouse_full_name');
            $table->string('spouse_education_level')->nullable()->after('spouse_date_of_birth');
            $table->string('spouse_profession')->nullable()->after('spouse_education_level');
            $table->string('spouse_nida_number')->nullable()->after('spouse_profession');
            $table->string('spouse_email')->nullable()->after('spouse_nida_number');
            $table->string('spouse_phone_number')->nullable()->after('spouse_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'spouse_alive',
                'spouse_full_name',
                'spouse_date_of_birth',
                'spouse_education_level',
                'spouse_profession',
                'spouse_nida_number',
                'spouse_email',
                'spouse_phone_number'
            ]);
        });
    }
};
