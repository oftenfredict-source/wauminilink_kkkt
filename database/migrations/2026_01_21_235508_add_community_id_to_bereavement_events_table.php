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
        Schema::table('bereavement_events', function (Blueprint $table) {
            if (!Schema::hasColumn('bereavement_events', 'community_id')) {
                $table->foreignId('community_id')->nullable()->after('created_by')->constrained('communities')->onDelete('set null');
                $table->index('community_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bereavement_events', function (Blueprint $table) {
            if (Schema::hasColumn('bereavement_events', 'community_id')) {
                $table->dropForeign(['community_id']);
                $table->dropIndex(['community_id']);
                $table->dropColumn('community_id');
            }
        });
    }
};
