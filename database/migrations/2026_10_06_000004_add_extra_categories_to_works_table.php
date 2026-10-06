<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('works', function (Blueprint $table) {
            // Further categories a work also belongs to, comma separated (e.g. "web"): shown as extra labels and
            // included when visitors filter the works by that category.
            $table->string('extra_categories', 80)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn('extra_categories');
        });
    }
};
