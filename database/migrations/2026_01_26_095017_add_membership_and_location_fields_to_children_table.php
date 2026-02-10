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
        Schema::table('children', function (Blueprint $table) {
            // Membership status for children
            $table->boolean('is_church_member')->default(false)->after('date_of_birth')->comment('Whether this child is a church member');
            
            // Street and Fellowship assignment (only for member children)
            $table->string('street')->nullable()->after('is_church_member')->comment('Street (Mtaa) - only for member children');
            $table->foreignId('community_id')->nullable()->after('street')->constrained('communities')->onDelete('set null')->comment('Fellowship (Jumuiya) - only for member children');
            
            // Location fields for children living outside main church area
            $table->string('region')->nullable()->after('community_id')->comment('Region where child lives');
            $table->string('district')->nullable()->after('region')->comment('District where child lives');
            $table->string('city_town')->nullable()->after('district')->comment('City/Town where child lives');
            $table->string('current_church_attended')->nullable()->after('city_town')->comment('Current church attended (optional)');
            $table->boolean('lives_outside_main_area')->default(false)->after('current_church_attended')->comment('Flag indicating child lives outside main church area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropForeign(['community_id']);
            $table->dropColumn([
                'is_church_member',
                'street',
                'community_id',
                'region',
                'district',
                'city_town',
                'current_church_attended',
                'lives_outside_main_area'
            ]);
        });
    }
};
