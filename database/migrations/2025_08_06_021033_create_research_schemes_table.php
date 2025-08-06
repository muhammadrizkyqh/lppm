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
        Schema::create('research_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('default_fund_amount', 15, 2);
            $table->date('registration_start_date');
            $table->date('registration_end_date');
            $table->json('expected_outputs'); // ['jurnal_sinta_1', 'jurnal_sinta_2', 'buku_isbn', 'hki', 'produk_inovasi']
            $table->enum('type', ['penelitian', 'pengabdian']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_schemes');
    }
};
