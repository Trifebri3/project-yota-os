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
        Schema::table('sub_program_attendances', function (Blueprint $table) {
            $table->uuid('kegiatan_id')->nullable()->after('program_kerja_id');
            $table->foreign('kegiatan_id')->references('id')->on('kegiatan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_program_attendances', function (Blueprint $table) {
            $table->dropForeign(['kegiatan_id']);
            $table->dropColumn('kegiatan_id');
        });
    }
};
