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
        Schema::table('kpi_indicators', function (Blueprint $table) {
            if (!Schema::hasColumn('kpi_indicators', 'graphic_image')) {
                $table->string('graphic_image')->nullable()->after('evidence_doc');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_indicators', function (Blueprint $table) {
            if (Schema::hasColumn('kpi_indicators', 'graphic_image')) {
                $table->dropColumn('graphic_image');
            }
        });
    }
};
