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
        // 1. Update Users Table
        Schema::table('users', function (Blueprint $table) {
            $table->string('organisasi')->nullable();
            $table->string('photo_profil')->nullable();
        });

        // 2. Update Programs Table
        Schema::table('programs', function (Blueprint $table) {
            $table->string('logo_program')->nullable();
            $table->string('banner_program')->nullable();
            $table->text('tujuan_program')->nullable();
            $table->string('kategori_program')->nullable(); // Lingkungan, Pendidikan, Pemberdayaan Ekonomi, Penelitian, Kesehatan, Sosial
            $table->uuid('project_officer_id')->nullable();
            $table->uuid('supervisor_id')->nullable();
            $table->string('mitra')->nullable();
            $table->string('sponsor')->nullable();
            $table->string('organisasi_pelaksana')->nullable();
            $table->text('target_capaian')->nullable();

            $table->foreign('project_officer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
        });

        // 3. Update Zones Table
        Schema::table('zones', function (Blueprint $table) {
            $table->string('photo_zona')->nullable();
            $table->string('status_zona')->default('aktif'); // aktif, belum_aktif, monitoring, selesai
            $table->uuid('program_id')->nullable();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 4. Create Program-User Assignments Table
        Schema::create('program_user_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('program_id');
            $table->uuid('zone_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_user_assignments');

        Schema::table('zones', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropColumn(['photo_zona', 'status_zona', 'program_id']);
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign(['project_officer_id']);
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn([
                'logo_program', 'banner_program', 'tujuan_program', 'kategori_program',
                'project_officer_id', 'supervisor_id', 'mitra', 'sponsor', 'organisasi_pelaksana', 'target_capaian'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['organisasi', 'photo_profil']);
        });
    }
};
