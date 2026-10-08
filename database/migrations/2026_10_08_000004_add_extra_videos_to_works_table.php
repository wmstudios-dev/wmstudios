<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            // More videos for a project, one per line: "link | title (optional) | vertical (optional)".
            $table->text('extra_videos')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn('extra_videos');
        });
    }
};