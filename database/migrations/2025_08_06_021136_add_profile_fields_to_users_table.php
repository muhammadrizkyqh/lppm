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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nidn')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->enum('user_type', ['dosen', 'admin', 'reviewer', 'pimpinan'])->default('dosen');
            $table->enum('functional_position', ['asisten_ahli', 'lektor', 'lektor_kepala', 'guru_besar'])->nullable();
            $table->string('field_of_study')->nullable();
            $table->text('address')->nullable();
            $table->enum('employment_status', ['tetap', 'tidak_tetap'])->default('tetap');
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nidn', 'phone', 'user_type', 'functional_position',
                'field_of_study', 'address', 'employment_status', 'is_active'
            ]);
        });
    }
};
