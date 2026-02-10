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
        Schema::table('community_offerings', function (Blueprint $table) {
            $table->decimal('amount_umoja', 15, 2)->default(0)->after('amount');
            $table->decimal('amount_jengo', 15, 2)->default(0)->after('amount_umoja');
            $table->decimal('amount_ahadi', 15, 2)->default(0)->after('amount_jengo');
            $table->decimal('amount_other', 15, 2)->default(0)->after('amount_ahadi');
        });

        Schema::table('community_offering_items', function (Blueprint $table) {
            $table->decimal('amount_umoja', 15, 2)->default(0)->after('amount');
            $table->decimal('amount_jengo', 15, 2)->default(0)->after('amount_umoja');
            $table->decimal('amount_ahadi', 15, 2)->default(0)->after('amount_jengo');
            $table->decimal('amount_other', 15, 2)->default(0)->after('amount_ahadi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('community_offering_items', function (Blueprint $table) {
            $table->dropColumn(['amount_umoja', 'amount_jengo', 'amount_ahadi', 'amount_other']);
        });

        Schema::table('community_offerings', function (Blueprint $table) {
            $table->dropColumn(['amount_umoja', 'amount_jengo', 'amount_ahadi', 'amount_other']);
        });
    }
};
