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
        Schema::table('proposal_team_members', function (Blueprint $table) {
            // Informasi tambahan untuk dosen
            $table->string('position')->nullable(); // Jabatan fungsional
            $table->string('department')->nullable(); // Departemen/Prodi
            $table->string('expertise')->nullable(); // Bidang keahlian
            $table->string('phone')->nullable(); // Nomor telepon
            $table->string('email')->nullable(); // Email

            // Informasi tambahan untuk mahasiswa
            $table->string('study_program')->nullable(); // Program studi
            $table->integer('semester')->nullable(); // Semester
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_team_members', function (Blueprint $table) {
            $table->dropColumn([
                'position',
                'department',
                'expertise',
                'phone',
                'email',
                'study_program',
                'semester'
            ]);
        });
    }
};
