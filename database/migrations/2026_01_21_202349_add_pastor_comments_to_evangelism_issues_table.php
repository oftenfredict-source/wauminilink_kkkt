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
        Schema::table('evangelism_issues', function (Blueprint $table) {
            $table->text('pastor_comments')->nullable()->after('resolved_at');
            $table->timestamp('pastor_commented_at')->nullable()->after('pastor_comments');
            $table->foreignId('pastor_commented_by')->nullable()->constrained('users')->onDelete('set null')->after('pastor_commented_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evangelism_issues', function (Blueprint $table) {
            $table->dropForeign(['pastor_commented_by']);
            $table->dropColumn(['pastor_comments', 'pastor_commented_at', 'pastor_commented_by']);
        });
    }
};
