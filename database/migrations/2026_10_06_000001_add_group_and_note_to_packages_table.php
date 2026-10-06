<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Packages with the same group name are shown together under one heading (e.g. "Design").
            $table->string('group_id')->nullable();
            $table->string('group_en')->nullable();
            // Small print under a group (the first package in the group that has one is used), one line per note.
            $table->text('note_id')->nullable();
            $table->text('note_en')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['group_id', 'group_en', 'note_id', 'note_en']);
        });
    }
};
