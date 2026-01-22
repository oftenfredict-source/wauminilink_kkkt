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
            // Change issue_type to string to accommodate longer values and avoid enum limitations
            $table->string('issue_type')->change();
            
            // Ensure other potentially truncated columns are large enough
            $table->string('priority')->change();
            $table->string('status')->default('open')->change();
            
            // Ensure title and description are present and correct types
            // description should be text, not string (varchar)
            $table->text('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evangelism_issues', function (Blueprint $table) {
            // We can't easily revert to "enum" without knowing exact original definition
            // but we can leave them as strings which is safe.
        });
    }
};
