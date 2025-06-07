<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penelitians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('kategori_penelitian_id')->constrained()->onDelete('restrict');
            $table->string('judul');
            $table->string('bidang_ilmu');
            $table->string('mitra')->nullable();
            $table->year('tahun_usulan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('dana_diajukan', 15, 2);
            $table->string('file_proposal');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamps();

            // Indexes untuk performa
            $table->index(['user_id', 'tahun_usulan']);
            $table->index('status');
            $table->index('kategori_penelitian_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penelitians');
    }
};
