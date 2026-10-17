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
        // 1. Alter program_kerja (Sub Program) table
        Schema::table('program_kerja', function (Blueprint $table) {
            $table->string('qr_code')->nullable()->unique();
            $table->string('attendance_token')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('radius_meters')->default(50);
        });

        // 2. Inventories table
        Schema::create('inventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('asset_id')->unique();
            $table->string('name');
            $table->string('category');
            $table->string('sub_category')->nullable();
            $table->uuid('program_id');
            $table->string('location');
            $table->integer('quantity')->default(1);
            $table->string('unit')->default('pcs');
            $table->string('initial_condition');
            $table->string('current_condition');
            $table->decimal('price_per_unit', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->date('purchase_date')->nullable();
            $table->string('funding_source')->nullable();
            $table->string('supplier')->nullable();
            $table->uuid('pic_id'); // User ID responsible
            $table->boolean('status_active')->default(true);
            $table->boolean('status_damaged')->default(false);
            $table->boolean('status_lost')->default(false);
            $table->boolean('status_borrowed')->default(false);
            $table->text('description')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('pic_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. Inventory logs (Audit trails - permanent & undeletable)
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('inventory_id');
            $table->uuid('user_id');
            $table->string('action'); // 'create', 'update', 'delete', 'audit'
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('location')->nullable(); // coordinates or text
            $table->timestamp('created_at')->nullable();

            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 4. Sub Program attendances (QR code attendance)
        Schema::create('sub_program_attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('program_id');
            $table->uuid('program_kerja_id'); // Sub Program ID
            $table->dateTime('scan_time');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('device_id')->nullable();
            $table->string('status')->default('valid'); // 'valid', 'invalid'
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('program_kerja_id')->references('id')->on('program_kerja')->onDelete('cascade');
        });

        // 5. Documentations table (universal uploads & metadata)
        Schema::create('documentations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('program_id')->nullable();
            $table->uuid('program_kerja_id')->nullable(); // sub program
            $table->uuid('activity_id')->nullable();
            $table->string('file_path');
            $table->string('user_name');
            $table->string('program_name')->nullable();
            $table->string('sub_program_name')->nullable();
            $table->dateTime('timestamp');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('device_info')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
            $table->foreign('program_kerja_id')->references('id')->on('program_kerja')->onDelete('set null');
            $table->foreign('activity_id')->references('id')->on('kegiatan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentations');
        Schema::dropIfExists('sub_program_attendances');
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('inventories');

        Schema::table('program_kerja', function (Blueprint $table) {
            $table->dropColumn(['qr_code', 'attendance_token', 'latitude', 'longitude', 'radius_meters']);
        });
    }
};
