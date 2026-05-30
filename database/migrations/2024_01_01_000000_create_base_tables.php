<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Sessions / Cache / Jobs ──────────────────────────────────────────
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // ── Schools ──────────────────────────────────────────────────────────
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->char('initial', 1)->default('M');
            $table->integer('student_count')->default(0);
            $table->enum('plan', ['starter', 'school', 'pro'])->default('starter');
            $table->boolean('is_active')->default(true);
            $table->date('joined_at')->nullable();
            $table->timestamps();
        });

        // ── Users ────────────────────────────────────────────────────────────
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['student', 'teacher', 'director', 'admin'])->default('student');
            $table->foreignId('school_id')->nullable()->constrained()->nullOnDelete();
            $table->string('avatar_path')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->date('joined_at')->nullable();
            $table->unsignedInteger('notifications_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('google_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // ── Groups (classes & clubs) ─────────────────────────────────────────
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('kind', ['class', 'club'])->default('class');
            $table->enum('color', ['blue', 'saffron', 'atlas', 'terracotta'])->default('blue');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->unsignedInteger('member_count')->default(0);
            $table->timestamps();
        });

        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['member', 'moderator'])->default('member');
            $table->timestamps();
            $table->unique(['group_id', 'user_id']);
        });

        // ── Posts ────────────────────────────────────────────────────────────
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('body');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_announcement')->default(false);
            $table->enum('visibility', ['school', 'group', 'public'])->default('school');
            $table->float('ai_score')->nullable();
            $table->boolean('ai_flagged')->default(false);
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedInteger('sparks_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Post attachments ─────────────────────────────────────────────────
        Schema::create('post_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->enum('kind', ['image', 'file', 'poll']);
            $table->string('path')->nullable();
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ── Polls ────────────────────────────────────────────────────────────
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attachment_id')->constrained('post_attachments')->cascadeOnDelete();
            $table->string('question');
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->unsignedInteger('votes_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
        });

        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('poll_options')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['poll_id', 'user_id']);
        });

        // ── Reactions ────────────────────────────────────────────────────────
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['like', 'spark', 'check'])->default('like');
            $table->timestamps();
            $table->unique(['user_id', 'post_id', 'type']);
        });

        // ── Comments ─────────────────────────────────────────────────────────
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Events ───────────────────────────────────────────────────────────
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('starts_at');
            $table->string('location')->nullable();
            $table->enum('color', ['blue', 'saffron', 'atlas', 'terracotta'])->default('blue');
            $table->timestamps();
        });

        // ── Messages ─────────────────────────────────────────────────────────
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // ── Notifications ────────────────────────────────────────────────────
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // ── Moderation reports ───────────────────────────────────────────────
        Schema::create('moderation_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('comment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason');
            $table->float('ai_score')->nullable();
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'approved', 'rejected', 'ignored'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        // ── Badges ───────────────────────────────────────────────────────────
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->enum('color', ['blue', 'saffron', 'atlas', 'terracotta'])->default('blue');
            $table->date('awarded_at');
            $table->timestamps();
        });

        // ── Hashtags ─────────────────────────────────────────────────────────
        Schema::create('hashtags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('posts_count')->default(0);
            $table->timestamps();
        });

        Schema::create('post_hashtag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hashtag_id')->constrained()->cascadeOnDelete();
            $table->primary(['post_id', 'hashtag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_hashtag');
        Schema::dropIfExists('hashtags');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('moderation_reports');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('events');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('reactions');
        Schema::dropIfExists('poll_votes');
        Schema::dropIfExists('poll_options');
        Schema::dropIfExists('polls');
        Schema::dropIfExists('post_attachments');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('schools');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
