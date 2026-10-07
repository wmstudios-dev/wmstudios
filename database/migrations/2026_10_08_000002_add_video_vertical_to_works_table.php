<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            // The video is vertical (9:16), e.g. a reel or a phone video. Shown in a tall player instead of a wide one.
            $table->boolean('video_vertical')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn('video_vertical');
        });
    }
};
