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
        Schema::create('research_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['jurnal_sinta_1', 'jurnal_sinta_2', 'jurnal_sinta_3', 'jurnal_sinta_4', 'jurnal_sinta_5', 'jurnal_sinta_6', 'jurnal_scopus', 'buku_isbn', 'hki', 'produk_inovasi']);
            $table->string('journal_name')->nullable(); // Untuk publikasi jurnal
            $table->string('issn')->nullable();
            $table->string('isbn')->nullable(); // Untuk buku
            $table->string('hki_number')->nullable(); // Untuk HKI
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'submitted', 'published', 'verified'])->default('draft');
            $table->date('publication_date')->nullable();
            $table->text('publication_url')->nullable();
            $table->boolean('verified_by_admin')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_outputs');
    }
};
