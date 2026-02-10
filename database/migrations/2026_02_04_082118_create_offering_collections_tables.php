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
        Schema::create('offering_collection_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('collection_date');
            $table->foreignId('campus_id')->constrained()->onDelete('cascade'); // Mtaa
            $table->foreignId('lead_elder_id')->constrained('users'); // Collector
            $table->enum('status', ['draft', 'submitted', 'received'])->default('draft');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->foreignId('received_by')->nullable()->constrained('users'); // Treasurer
            $table->dateTime('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('offering_collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offering_collection_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('community_id')->constrained()->onDelete('cascade'); // Jumuiya
            $table->decimal('amount_unity', 15, 2)->default(0);
            $table->decimal('amount_building', 15, 2)->default(0);
            $table->decimal('amount_pledge', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offering_collection_items');
        Schema::dropIfExists('offering_collection_sessions');
    }
};
