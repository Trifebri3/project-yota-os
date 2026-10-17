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
        // 1. Update programs table
        Schema::table('programs', function (Blueprint $table) {
            $table->time('check_in_start')->default('07:00:00');
            $table->time('check_in_end')->default('08:00:00');
            $table->time('check_out_start')->default('17:00:00');
            $table->time('check_out_end')->default('18:00:00');
        });

        // 2. Update program_kerja table
        Schema::table('program_kerja', function (Blueprint $table) {
            $table->json('target_outputs')->nullable(); // [{"label": "UMKM", "target": 30, "achieved": 0}]
        });

        // 3. Create agendas table
        Schema::create('agendas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('location')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 4. Create routine_activities table
        Schema::create('routine_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_selfie_required')->default(false);
            $table->boolean('is_notes_required')->default(false);
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 5. Create routine_activity_logs table
        Schema::create('routine_activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('routine_activity_id');
            $table->date('date');
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('routine_activity_id')->references('id')->on('routine_activities')->onDelete('cascade');
        });

        // 6. Create additional_activities table
        Schema::create('additional_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('program_id');
            $table->string('name');
            $table->text('description');
            $table->date('date');
            $table->decimal('hours_spent', 5, 2);
            $table->string('photo_path')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 7. Create task_attendances table
        Schema::create('task_attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('task_id');
            $table->string('location')->nullable();
            $table->string('photo_path')->nullable();
            $table->dateTime('time');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });

        // 8. Create program_resources table
        Schema::create('program_resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('name');
            $table->string('file_path');
            $table->string('type'); // guide, SOP, template, letter
            $table->string('distribution')->default('general'); // general, specific
            $table->uuid('assigned_user_id')->nullable();
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 9. Create participant_documents table
        Schema::create('participant_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('program_id');
            $table->string('name');
            $table->string('file_path');
            $table->string('type')->default('certificate'); // certificate, piagam, letter, other
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 10. Create facilities table
        Schema::create('facilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 11. Create user_facilities table
        Schema::create('user_facilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('facility_id');
            $table->string('status')->default('pending'); // pending, given
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
        });

        // 12. Create complaints table
        Schema::create('complaints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('program_id');
            $table->string('category'); // facilities, security, health, administrative, other
            $table->string('title');
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->string('status')->default('pending'); // pending, processing, resolved, closed
            $table->text('response')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 13. Create reprimands table
        Schema::create('reprimands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('program_id');
            $table->string('level'); // light, medium, heavy
            $table->text('reason');
            $table->string('proof_path')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 14. Create daily_reports table
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->date('date');
            $table->text('issues')->nullable();
            $table->text('risks')->nullable();
            $table->text('roadblocks')->nullable();
            $table->text('recommendations')->nullable();
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
        Schema::dropIfExists('reprimands');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('user_facilities');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('participant_documents');
        Schema::dropIfExists('program_resources');
        Schema::dropIfExists('task_attendances');
        Schema::dropIfExists('additional_activities');
        Schema::dropIfExists('routine_activity_logs');
        Schema::dropIfExists('routine_activities');
        Schema::dropIfExists('agendas');

        Schema::table('program_kerja', function (Blueprint $table) {
            $table->dropColumn(['target_outputs']);
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['check_in_start', 'check_in_end', 'check_out_start', 'check_out_end']);
        });
    }
};
