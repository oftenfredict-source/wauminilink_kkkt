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
            $table->enum('baptism_status', ['baptized', 'not_baptized'])->nullable()->after('date_of_birth');
            $table->date('baptism_date')->nullable()->after('baptism_status');
            $table->string('baptism_location')->nullable()->after('baptism_date');
            $table->string('baptized_by')->nullable()->after('baptism_location');
            $table->string('baptism_certificate_number')->nullable()->after('baptized_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn([
                'baptism_status',
                'baptism_date',
                'baptism_location',
                'baptized_by',
                'baptism_certificate_number'
            ]);
        });
    }
};
