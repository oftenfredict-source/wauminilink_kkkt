<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deleted_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->json('member_snapshot');
            $table->string('reason');
            $table->timestamp('deleted_at_actual');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deleted_members');
    }
};



