<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_photos', function (Blueprint $table) {
            // What kind of piece the picture is (feed post, story, carousel slide, ...), so a work page can group them.
            $table->string('kind', 20)->default('other');
        });
    }

    public function down(): void
    {
        Schema::table('work_photos', function (Blueprint $table) {
            $table->dropColumn('kind');
        });
    }
};
