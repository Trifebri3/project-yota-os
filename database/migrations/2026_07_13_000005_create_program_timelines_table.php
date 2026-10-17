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
        // 1. Create Program Timelines table
        Schema::create('program_timelines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 2. Update program_kerja table
        Schema::table('program_kerja', function (Blueprint $table) {
            $table->uuid('program_timeline_id')->nullable();
            $table->foreign('program_timeline_id')->references('id')->on('program_timelines')->onDelete('set null');
        });

        // 3. Update kegiatan table
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->uuid('program_timeline_id')->nullable();
            $table->foreign('program_timeline_id')->references('id')->on('program_timelines')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropForeign(['program_timeline_id']);
            $table->dropColumn(['program_timeline_id']);
        });

        Schema::table('program_kerja', function (Blueprint $table) {
            $table->dropForeign(['program_timeline_id']);
            $table->dropColumn(['program_timeline_id']);
        });

        Schema::dropIfExists('program_timelines');
    }
};
