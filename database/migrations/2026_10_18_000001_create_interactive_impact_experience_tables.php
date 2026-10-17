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
        // 1. Story Milestones (Daily Journey & Documentary timeline)
        Schema::create('story_milestones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->integer('day_number')->default(1);
            $table->string('phase')->default('Discover'); // Discover, Design, Build, Deploy, Measure
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('story_narrative');
            $table->string('activity_name')->nullable();
            $table->string('personnel_involved')->nullable();
            $table->text('key_achievement')->nullable();
            $table->date('date');
            $table->json('photos')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 2. The Impact Chain (Input -> Activity -> Output -> Outcome -> Impact)
        Schema::create('impact_chains', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('stage'); // input, activity, output, outcome, impact
            $table->integer('stage_order')->default(1); // 1 to 5
            $table->string('title');
            $table->text('description');
            $table->string('metric_value')->nullable();
            $table->string('metric_unit')->nullable();
            $table->string('target_value')->nullable();
            $table->text('evidence_note')->nullable();
            $table->string('verification_status')->default('verified');
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 3. KPI Indicators & Evidence Control Room
        Schema::create('kpi_indicators', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('code')->nullable(); // e.g. KPI-01
            $table->string('name');
            $table->string('category')->default('General'); // Waste, Carbon, Community, Economy, Health
            $table->text('definition')->nullable();
            $table->decimal('baseline', 12, 2)->default(0);
            $table->decimal('target', 12, 2)->default(0);
            $table->decimal('actual', 12, 2)->default(0);
            $table->string('unit')->default('unit');
            $table->text('measurement_method')->nullable();
            $table->string('verified_by')->nullable();
            $table->string('status')->default('achieved'); // achieved, in_progress, pending
            $table->string('evidence_doc')->nullable();
            $table->json('trend_data')->nullable();
            $table->json('carbon_breakdown')->nullable(); // { produced: x, avoided: y, sequestered: z }
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 4. Project Outputs (Prototypes, Publications, HAKI, Digital Products)
        Schema::create('project_outputs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('category'); // prototype, publication, ip_recognition, digital
            $table->string('title');
            $table->text('description');
            $table->string('thumbnail')->nullable();
            $table->date('release_date')->nullable();
            $table->string('authors_or_team')->nullable();
            $table->string('evidence_url')->nullable();
            $table->string('ip_number')->nullable();
            $table->string('status')->default('published');
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });

        // 5. Before & After comparison records
        Schema::create('before_after_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->string('title');
            $table->string('location')->nullable();
            $table->string('before_image');
            $table->string('after_image');
            $table->date('before_date')->nullable();
            $table->date('after_date')->nullable();
            $table->text('before_description')->nullable();
            $table->text('after_description')->nullable();
            $table->string('metric_impact')->nullable();
            $table->timestamps();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('before_after_records');
        Schema::dropIfExists('project_outputs');
        Schema::dropIfExists('kpi_indicators');
        Schema::dropIfExists('impact_chains');
        Schema::dropIfExists('story_milestones');
    }
};
