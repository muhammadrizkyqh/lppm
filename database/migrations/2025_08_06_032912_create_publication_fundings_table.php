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
        Schema::create('publication_fundings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pengaju
            $table->string('title'); // Judul publikasi
            $table->enum('publication_type', ['journal_national', 'journal_international', 'conference_national', 'conference_international', 'book', 'book_chapter']);
            $table->string('journal_name')->nullable(); // Nama jurnal/conference
            $table->string('publisher')->nullable(); // Penerbit
            $table->text('abstract'); // Abstrak artikel
            $table->decimal('estimated_cost', 15, 2); // Estimasi biaya
            $table->text('cost_breakdown'); // Rincian biaya
            $table->date('submission_deadline')->nullable(); // Target deadline submit
            $table->date('expected_publication_date')->nullable(); // Target terbit
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'published'])->default('draft');
            $table->text('reviewer_notes')->nullable(); // Catatan reviewer
            $table->decimal('approved_amount', 15, 2)->nullable(); // Jumlah yang disetujui
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('publication_url')->nullable(); // URL publikasi jika sudah terbit
            $table->string('doi')->nullable(); // DOI artikel
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_fundings');
    }
};
