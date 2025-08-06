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
        Schema::create('submission_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Periode 1 - 2025", "Periode 2 - 2025"
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('year');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_periods');
    }
};
