<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luaran_penelitians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penelitian_id')->constrained()->onDelete('cascade');
            $table->string('jenis_luaran'); // jurnal, buku, HKI, prosiding, dll
            $table->string('judul_luaran');
            $table->string('media_publikasi')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->string('file_luaran')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index('penelitian_id');
            $table->index('jenis_luaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luaran_penelitians');
    }
};
