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
        Schema::create('proposal_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Null jika mahasiswa
            $table->string('name'); // Nama anggota (bisa dosen atau mahasiswa)
            $table->string('nidn')->nullable(); // NIDN untuk dosen
            $table->string('nim')->nullable(); // NIM untuk mahasiswa
            $table->enum('member_type', ['dosen', 'mahasiswa']);
            $table->enum('role', ['ketua', 'anggota'])->default('anggota');
            $table->timestamps();

            $table->unique(['proposal_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_team_members');
    }
};
