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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('research_scheme_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ketua tim
            $table->text('abstract');
            $table->string('keywords');
            $table->decimal('fund_amount', 15, 2);
            $table->text('publication_plan')->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'revision_required', 'approved', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');

            // Status Pencairan Dana
            $table->boolean('fund_stage_1_confirmed')->default(false);
            $table->timestamp('fund_stage_1_confirmed_at')->nullable();
            $table->foreignId('fund_stage_1_confirmed_by')->nullable()->constrained('users');

            $table->boolean('fund_stage_2_confirmed')->default(false);
            $table->timestamp('fund_stage_2_confirmed_at')->nullable();
            $table->foreignId('fund_stage_2_confirmed_by')->nullable()->constrained('users');

            $table->boolean('fund_stage_3_confirmed')->default(false);
            $table->timestamp('fund_stage_3_confirmed_at')->nullable();
            $table->foreignId('fund_stage_3_confirmed_by')->nullable()->constrained('users');

            // Status Monitoring
            $table->boolean('monitoring_1_confirmed')->default(false);
            $table->timestamp('monitoring_1_confirmed_at')->nullable();
            $table->foreignId('monitoring_1_confirmed_by')->nullable()->constrained('users');

            $table->boolean('monitoring_2_confirmed')->default(false);
            $table->timestamp('monitoring_2_confirmed_at')->nullable();
            $table->foreignId('monitoring_2_confirmed_by')->nullable()->constrained('users');

            // Sanksi
            $table->boolean('has_penalty')->default(false);
            $table->text('penalty_reason')->nullable();
            $table->decimal('penalty_amount', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
