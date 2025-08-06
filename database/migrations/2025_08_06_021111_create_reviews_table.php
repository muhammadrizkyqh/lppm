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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users');

            // Kriteria Penilaian (1-5 scale)
            $table->integer('relevance_score')->nullable();
            $table->integer('methodology_score')->nullable();
            $table->integer('originality_score')->nullable();
            $table->integer('feasibility_score')->nullable();
            $table->decimal('total_score', 5, 2)->nullable();

            $table->text('relevance_comment')->nullable();
            $table->text('methodology_comment')->nullable();
            $table->text('originality_comment')->nullable();
            $table->text('feasibility_comment')->nullable();
            $table->text('general_comment')->nullable();

            $table->enum('recommendation', ['approved_without_revision', 'approved_with_revision', 'rejected'])->nullable();
            $table->enum('status', ['assigned', 'in_progress', 'completed'])->default('assigned');

            $table->timestamp('assigned_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['proposal_id', 'reviewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
