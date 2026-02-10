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
        Schema::create('ahadi_pledges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('community_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('campus_id')->nullable()->constrained()->onDelete('set null');
            $table->year('year');
            
            // Predefined items: Ng'ombe, Mbuzi, Kondoo, Kuku, Maziwa, Mayai, Kahawa, Ndizi, 
            // Mahindi, Maharagwe, Makopa, Mboga, Miwa, Ngano, Vifaa vya Sanaa, 
            // Bidhaa za Ufundi, Bidhaa za Viwanda, Vinginevyo
            $table->string('item_type'); 
            
            $table->decimal('quantity_promised', 15, 2);
            $table->string('unit')->nullable(); // e.g., heads, bags, kg, trays
            $table->decimal('estimated_value', 15, 2)->nullable();
            
            $table->decimal('quantity_fulfilled', 15, 2)->default(0);
            $table->date('fulfillment_date')->nullable();
            
            $table->enum('status', ['promised', 'partially_fulfilled', 'fully_fulfilled'])->default('promised');
            
            $table->string('recorded_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexing for faster reporting
            $table->index(['member_id', 'year']);
            $table->index(['community_id', 'year']);
            $table->index(['campus_id', 'year']);
            $table->index(['item_type', 'year']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahadi_pledges');
    }
};
