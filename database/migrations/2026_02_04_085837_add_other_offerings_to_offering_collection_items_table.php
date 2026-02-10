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
        Schema::table('offering_collection_items', function (Blueprint $table) {
            $table->decimal('amount_other', 10, 2)->default(0)->after('amount_pledge')
                ->comment('Other offerings collected outside envelopes (loose cash)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offering_collection_items', function (Blueprint $table) {
            $table->dropColumn('amount_other');
        });
    }
};
