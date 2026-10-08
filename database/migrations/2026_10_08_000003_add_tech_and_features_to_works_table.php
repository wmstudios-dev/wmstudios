<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            // For website projects: the tools used (comma separated) and a bullet list of the main features.
            $table->string('tech')->nullable();
            $table->text('features_en')->nullable();
            $table->text('features_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn(['tech', 'features_en', 'features_id']);
        });
    }
};
