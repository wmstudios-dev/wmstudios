<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Portfolio. category: photo, video, design, web, social.
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_en');
            $table->string('title_id');
            $table->string('client')->nullable();
            $table->string('category', 20)->index();
            $table->text('summary_en')->nullable();
            $table->text('summary_id')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_id')->nullable();
            $table->string('cover_photo')->nullable();
            $table->string('video_url')->nullable();      // YouTube / Vimeo / Instagram / direct file
            $table->string('project_url')->nullable();    // live site for web projects
            $table->string('before_photo')->nullable();
            $table->string('after_photo')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('work_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('icon', 30)->default('spark');
            $table->string('title_en');
            $table->string('title_id');
            $table->text('summary_en')->nullable();
            $table->text('summary_id')->nullable();
            $table->text('details_en')->nullable();   // one deliverable per line
            $table->text('details_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_id');
            $table->string('tagline_en')->nullable();
            $table->string('tagline_id')->nullable();
            $table->string('price_label_en')->nullable(); // free text, e.g. "From Rp 2.5M", empty = "Ask us"
            $table->string('price_label_id')->nullable();
            $table->text('features_en')->nullable();      // one feature per line
            $table->text('features_id')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question_en');
            $table->string('question_id');
            $table->text('answer_en');
            $table->text('answer_id');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('process_steps', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_id');
            $table->text('description_en')->nullable();
            $table->text('description_id')->nullable();
            $table->string('icon', 30)->default('spark');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Behind the scenes / daily moments / workspace. tag: workspace, moments, culture, learning, bts.
        Schema::create('space_items', function (Blueprint $table) {
            $table->id();
            $table->string('photo');
            $table->string('tag', 20)->default('moments')->index();
            $table->string('caption_en')->nullable();
            $table->string('caption_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Blog / "Thoughts"
        Schema::create('thoughts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_en');
            $table->string('title_id');
            $table->text('excerpt_en')->nullable();
            $table->text('excerpt_id')->nullable();
            $table->longText('body_en')->nullable();
            $table->longText('body_id')->nullable();
            $table->string('cover_photo')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('quote_en');
            $table->text('quote_id');
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('service')->nullable();
            $table->string('budget')->nullable();
            $table->text('message');
            $table->string('locale', 5)->default('id');
            $table->string('status', 10)->default('new')->index(); // new, read, replied
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });

        // Key/value site settings editable from the admin (contact info, socials, hero copy...).
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['settings', 'contact_messages', 'testimonials', 'clients', 'thoughts', 'space_items', 'process_steps', 'faqs', 'packages', 'services', 'work_photos', 'works'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
