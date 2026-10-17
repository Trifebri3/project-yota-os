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
        // Custom Forms table
        Schema::create('custom_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->json('fields'); // array of form fields definition
            $table->string('target_type')->default('generic'); // e.g. activity, task, survey
            $table->timestamps();
            $table->softDeletes();
        });

        // Zones table
        Schema::create('zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('attendance'); // 'attendance', 'task', 'activity'
            $table->json('geojson'); // GeoJSON Polygon coordinates
            $table->timestamps();
            $table->softDeletes();
        });

        // Programs table
        Schema::create('programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('status')->default('draft'); // 'draft', 'active', 'completed'
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('settings')->nullable(); // holds dashboard widgets, approval workflow, certificate templates, LPJ template
            $table->json('stats')->nullable(); // holds carbon offsets, SDGs targets, impact outcomes
            $table->timestamps();
            $table->softDeletes();
        });

        // Project Charters
        Schema::create('project_charters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->text('background')->nullable();
            $table->text('scope')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->json('stakeholder_info')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // Program Kerja (Work Plans)
        Schema::create('program_kerja', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->string('status')->default('pending'); // 'pending', 'active', 'completed'
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // Kegiatan (Activities)
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('work_plan_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('pending'); // 'pending', 'active', 'completed'
            $table->uuid('zone_id')->nullable(); // Geofenced area link
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('work_plan_id')->references('id')->on('program_kerja')->onDelete('cascade');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('set null');
        });

        // Tasks
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('activity_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('status')->default('pending'); // 'pending', 'in_progress', 'review', 'completed'
            $table->string('type')->default('individual'); // 'individual', 'group', 'division'
            $table->json('assigned_to')->nullable(); // array of user UUIDs or division strings
            $table->json('dependencies')->nullable(); // array of task UUIDs this task depends on
            $table->json('checklists')->nullable(); // array of tasks items: {label, checked}
            $table->json('custom_fields')->nullable(); // dynamic field values mapping
            $table->uuid('form_id')->nullable(); // linked form engine configuration
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('activity_id')->references('id')->on('kegiatan')->onDelete('cascade');
            $table->foreign('form_id')->references('id')->on('custom_forms')->onDelete('set null');
        });

        // Attendances (Geofenced, selfie-based check-in/out)
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('activity_id');
            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();
            $table->json('check_in_gps')->nullable(); // {lat, lng}
            $table->json('check_out_gps')->nullable(); // {lat, lng}
            $table->string('check_in_selfie')->nullable(); // file path
            $table->string('check_out_selfie')->nullable(); // file path
            $table->string('check_in_status')->default('absent'); // 'inside_zone', 'outside_zone', 'absent'
            $table->string('check_out_status')->default('absent');
            $table->text('reason')->nullable(); // justification if outside zone or exception
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('kegiatan')->onDelete('cascade');
        });

        // Submissions (Task outputs)
        Schema::create('submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id');
            $table->uuid('user_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('content_data')->nullable(); // output form values
            $table->string('signature_path')->nullable(); // signature image
            $table->string('selfie_path')->nullable(); // validation image
            $table->json('gps_location')->nullable(); // {lat, lng, inside_zone}
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
            $table->uuid('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->text('remarks')->nullable(); // feedback
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Logbooks
        Schema::create('logbooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('program_id');
            $table->string('title');
            $table->text('content');
            $table->date('date');
            $table->decimal('hours_spent', 4, 1)->default(0);
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // Finance Transactions (Reimbursements, SPJ, Invoices)
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('type'); // 'income', 'expense'
            $table->string('category')->default('operational'); // 'operational', 'logistics', 'transport', 'catering'
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
            $table->string('spj_document_path')->nullable(); // report document
            $table->string('receipt_path')->nullable(); // proof of payment
            $table->uuid('created_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Certificates
        Schema::create('certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->uuid('user_id');
            $table->string('certificate_number')->unique();
            $table->date('issue_date');
            $table->string('download_path')->nullable();
            $table->string('verification_code')->unique(); // UUID / Shortcode verification
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Audit Logs / Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('action'); // 'create', 'update', 'delete', 'auth'
            $table->string('model_type')->nullable();
            $table->string('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Temporary Signatures for QR Code signing sessions
        Schema::create('temporary_signatures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('session_token')->unique();
            $table->text('signature_data')->nullable(); // Base64 data representation of signatures
            $table->string('status')->default('pending'); // 'pending', 'completed'
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_signatures');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('finance_transactions');
        Schema::dropIfExists('logbooks');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('kegiatan');
        Schema::dropIfExists('program_kerja');
        Schema::dropIfExists('project_charters');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('custom_forms');
    }
};
