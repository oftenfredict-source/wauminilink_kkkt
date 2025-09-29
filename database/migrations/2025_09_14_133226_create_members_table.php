<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_members_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone_number');
            $table->date('date_of_birth')->nullable();
            $table->string('nida_number')->nullable();
            $table->string('tribe')->nullable();
            $table->string('other_tribe')->nullable();
            $table->string('region');
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->string('street')->nullable();
            $table->text('address')->nullable();
            $table->enum('living_with_family', ['yes', 'no'])->nullable();
            $table->string('family_relationship')->nullable();
            $table->string('profile_picture')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
};