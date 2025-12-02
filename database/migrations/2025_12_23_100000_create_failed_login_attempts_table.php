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
        Schema::create('failed_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable(); // Email or username attempted
            $table->string('ip_address', 45)->nullable();
            $table->string('mac_address')->nullable(); // MAC address if available
            $table->text('user_agent')->nullable();
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('browser')->nullable();
            $table->string('os')->nullable(); // Operating system
            $table->string('device_name')->nullable();
            $table->text('failure_reason')->nullable(); // Invalid credentials, account blocked, etc.
            $table->boolean('ip_blocked')->default(false);
            $table->timestamp('ip_blocked_at')->nullable();
            $table->timestamp('ip_unblocked_at')->nullable();
            $table->timestamps();
            
            $table->index(['ip_address', 'created_at']);
            $table->index('email');
            $table->index('ip_blocked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_login_attempts');
    }
};


