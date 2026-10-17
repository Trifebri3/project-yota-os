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
        Schema::create('budget_ceilings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('source'); // e.g. "CSR Yota", "Corporate Sponsor", "Donatur"
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->uuid('budget_ceiling_id')->nullable()->after('program_id');
            $table->foreign('budget_ceiling_id')->references('id')->on('budget_ceilings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_transactions', function (Blueprint $table) {
            $table->dropForeign(['budget_ceiling_id']);
            $table->dropColumn('budget_ceiling_id');
        });

        Schema::dropIfExists('budget_ceilings');
    }
};
