<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            // Reviews are shown as a chat: a screenshot of the real conversation, or a chat drawn from the text.
            $table->string('chat_image')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('channel')->nullable();
            $table->text('reply_en')->nullable();
            $table->text('reply_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['chat_image', 'rating', 'channel', 'reply_en', 'reply_id']);
        });
    }
};
