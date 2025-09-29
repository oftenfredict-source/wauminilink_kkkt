<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('members', function (Blueprint $table) {
            $table->string('mother_alive')->nullable();
            $table->string('mother_full_name')->nullable();
            $table->date('mother_date_of_birth')->nullable();
            $table->string('mother_education_level')->nullable();
            $table->string('mother_profession')->nullable();
            $table->string('mother_nida_number')->nullable();
            $table->string('mother_email')->nullable();
            $table->string('mother_phone_number')->nullable();
        });
    }
    public function down() {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'mother_alive',
                'mother_full_name',
                'mother_date_of_birth',
                'mother_education_level',
                'mother_profession',
                'mother_nida_number',
                'mother_email',
                'mother_phone_number',
            ]);
        });
    }
};
