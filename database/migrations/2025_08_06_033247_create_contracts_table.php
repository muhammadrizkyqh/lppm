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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique(); // Nomor kontrak
            $table->foreignId('proposal_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Judul kontrak
            $table->enum('type', ['research', 'service', 'collaboration']); // Jenis kontrak
            $table->text('description')->nullable(); // Deskripsi kontrak
            $table->decimal('contract_value', 15, 2); // Nilai kontrak
            $table->date('start_date'); // Tanggal mulai
            $table->date('end_date'); // Tanggal berakhir
            $table->integer('duration_months'); // Durasi dalam bulan

            // Pihak yang terlibat
            $table->string('first_party'); // Pihak pertama (institusi)
            $table->string('first_party_representative'); // Wakil pihak pertama
            $table->string('first_party_position'); // Jabatan wakil pihak pertama
            $table->foreignId('second_party_user_id')->constrained('users'); // Pihak kedua (peneliti)
            $table->string('second_party_position'); // Jabatan peneliti

            // Status kontrak
            $table->enum('status', ['draft', 'pending_signature', 'signed', 'active', 'completed', 'terminated'])->default('draft');

            // Tanggal-tanggal penting
            $table->timestamp('signed_at')->nullable(); // Tanggal ditandatangani
            $table->foreignId('signed_by_first_party')->nullable()->constrained('users'); // Yang menandatangani dari pihak pertama
            $table->timestamp('second_party_signed_at')->nullable(); // Tanggal ditandatangani pihak kedua

            // Deliverables dan milestone
            $table->json('deliverables')->nullable(); // Deliverables yang harus diselesaikan
            $table->json('payment_schedule')->nullable(); // Jadwal pembayaran

            // Dokumen
            $table->text('terms_and_conditions'); // Syarat dan ketentuan
            $table->text('special_conditions')->nullable(); // Ketentuan khusus

            // Template dan approval
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
