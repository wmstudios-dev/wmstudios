<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            // Optional headline result for a project, e.g. "250K" + "views in 30 days".
            $table->string('metric_value', 40)->nullable();
            $table->string('metric_label_id', 120)->nullable();
            $table->string('metric_label_en', 120)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn(['metric_value', 'metric_label_id', 'metric_label_en']);
        });
    }
};
