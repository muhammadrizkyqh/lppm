<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update functional_position enum to include new values
        DB::statement("ALTER TABLE users MODIFY COLUMN functional_position ENUM('asisten_ahli', 'lektor', 'lektor_kepala', 'guru_besar', 'profesor', 'tenaga_pengajar', 'tidak_ada') NULL");

        // Update employment_status enum to include new values
        DB::statement("ALTER TABLE users MODIFY COLUMN employment_status ENUM('tetap', 'tidak_tetap', 'kontrak', 'honorer') DEFAULT 'tetap'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE users MODIFY COLUMN functional_position ENUM('asisten_ahli', 'lektor', 'lektor_kepala', 'guru_besar') NULL");
        DB::statement("ALTER TABLE users MODIFY COLUMN employment_status ENUM('tetap', 'tidak_tetap') DEFAULT 'tetap'");
    }
};
