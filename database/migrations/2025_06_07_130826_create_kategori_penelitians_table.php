<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_penelitians', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index('nama_kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_penelitians');
    }
};
