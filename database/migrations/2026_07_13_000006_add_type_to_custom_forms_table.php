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
        // 1. Update custom_forms table
        Schema::table('custom_forms', function (Blueprint $table) {
            $table->string('type')->default('individual'); // individual, group
        });

        // 2. Create carbon_logs table
        Schema::create('carbon_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('program_id');
            $table->uuid('form_id')->nullable();
            $table->string('type')->default('individual'); // individual, group
            $table->date('date');
            $table->json('content_data');
            $table->decimal('emissions', 10, 2)->default(0.00);
            $table->decimal('offsets', 10, 2)->default(0.00);
            $table->decimal('net_balance', 10, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('form_id')->references('id')->on('custom_forms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carbon_logs');

        Schema::table('custom_forms', function (Blueprint $table) {
            $table->dropColumn(['type']);
        });
    }
};
