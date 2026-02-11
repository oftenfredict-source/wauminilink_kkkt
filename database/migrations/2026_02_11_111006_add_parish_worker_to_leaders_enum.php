<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE leaders MODIFY COLUMN position ENUM('pastor','assistant_pastor','secretary','assistant_secretary','treasurer','assistant_treasurer','elder','deacon','deaconess','youth_leader','children_leader','worship_leader','choir_leader','usher_leader','evangelism_leader','prayer_leader','parish_worker','other') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE leaders MODIFY COLUMN position ENUM('pastor','assistant_pastor','secretary','assistant_secretary','treasurer','assistant_treasurer','elder','deacon','deaconess','youth_leader','children_leader','worship_leader','choir_leader','usher_leader','evangelism_leader','prayer_leader','other') NOT NULL");
    }
};
