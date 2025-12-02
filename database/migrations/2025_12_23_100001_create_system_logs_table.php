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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('level')->default('info'); // info, warning, error, critical
            $table->string('category')->nullable(); // security, performance, system, application
            $table->string('action')->nullable(); // login, logout, system_start, system_stop, etc.
            $table->text('message');
            $table->json('context')->nullable(); // Additional context data
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('browser')->nullable();
            $table->string('os')->nullable(); // Operating system
            $table->string('device_name')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('screen_resolution')->nullable();
            $table->string('timezone')->nullable();
            $table->string('language')->nullable();
            $table->json('device_properties')->nullable(); // Full device properties as JSON
            $table->string('route')->nullable();
            $table->string('method')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['level', 'created_at']);
            $table->index('category');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};


