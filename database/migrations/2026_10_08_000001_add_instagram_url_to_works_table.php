<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            // The client's Instagram account, for social media projects.
            $table->string('instagram_url', 300)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn('instagram_url');
        });
    }
};
