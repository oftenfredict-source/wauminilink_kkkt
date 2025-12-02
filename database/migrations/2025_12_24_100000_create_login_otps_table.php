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
        Schema::create('login_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('otp_code', 6); // 6-digit OTP
            $table->string('email'); // Email or member ID used for login
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->integer('attempts')->default(0); // Number of verification attempts
            $table->timestamps();
            
            $table->index(['user_id', 'is_used', 'expires_at']);
            $table->index(['email', 'is_used']);
            $table->index('otp_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_otps');
    }
};



