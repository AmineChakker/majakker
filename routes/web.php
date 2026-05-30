<?php

use App\Http\Controllers\{
    LandingController, FeedController, PostController, ReactionController,
    CommentController, PollController, ProfileController, GroupController,
    MessageController, EventController, NotificationController,
    SearchController, DashboardController, ModerationController, AnalyticsController,
    AttachmentController,
};
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'show'])->name('home');
require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

    // ── Feed ──────────────────────────────────────────────────────────────────
    Route::get('/feed', [FeedController::class, 'index'])->name('feed');

    // ── Posts ─────────────────────────────────────────────────────────────────
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/pin', [PostController::class, 'pin'])->name('posts.pin');
    Route::post('/posts/{post}/react', [ReactionController::class, 'toggle'])->name('posts.react');
    Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('posts.comments.index');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote');

    // ── File uploads ──────────────────────────────────────────────────────────
    Route::post('/attachments', [AttachmentController::class, 'store'])->name('attachments.store');

    // ── Profile ───────────────────────────────────────────────────────────────
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Groups (classes & clubs) ──────────────────────────────────────────────
    Route::get('/classes', [GroupController::class, 'indexClasses'])->name('classes');
    Route::get('/clubs', [GroupController::class, 'indexClubs'])->name('clubs');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::post('/groups/{group}/join', [GroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');

    // ── Messages ──────────────────────────────────────────────────────────────
    Route::get('/messages', [MessageController::class, 'index'])->name('messages');
    Route::get('/messages/{user}', [MessageController::class, 'conversation'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'send'])->name('messages.send');

    // ── Calendar / Events ─────────────────────────────────────────────────────
    Route::get('/calendar', [EventController::class, 'index'])->name('calendar');
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::patch('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // ── Notifications ─────────────────────────────────────────────────────────
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::patch('/notifications/{id}', [NotificationController::class, 'markRead'])->name('notifications.read');

    // ── Search ────────────────────────────────────────────────────────────────
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // ── Homework (teacher/student) ────────────────────────────────────────────
    Route::get('/homework', [FeedController::class, 'homework'])->name('homework');

    // ── Director routes ───────────────────────────────────────────────────────
    Route::middleware('role:director,admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
        Route::get('/dashboard/teachers', [DashboardController::class, 'teachers'])->name('dashboard.teachers');
        Route::get('/dashboard/students', [DashboardController::class, 'students'])->name('dashboard.students');
        Route::get('/dashboard/moderation', [ModerationController::class, 'index'])->name('moderation.index');
        Route::post('/dashboard/moderation/{report}/approve', [ModerationController::class, 'approve'])->name('moderation.approve');
        Route::post('/dashboard/moderation/{report}/reject', [ModerationController::class, 'reject'])->name('moderation.reject');
        Route::post('/dashboard/moderation/{report}/ignore', [ModerationController::class, 'ignore'])->name('moderation.ignore');
        Route::get('/dashboard/analytics', [AnalyticsController::class, 'school'])->name('analytics');
    });

    // ── Admin routes ──────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'show'])->name('dashboard');
        Route::get('/schools', [Admin\SchoolController::class, 'index'])->name('schools');
        Route::post('/schools', [Admin\SchoolController::class, 'store'])->name('schools.store');
        Route::get('/schools/{school}/edit', [Admin\SchoolController::class, 'edit'])->name('schools.edit');
        Route::patch('/schools/{school}', [Admin\SchoolController::class, 'update'])->name('schools.update');
        Route::delete('/schools/{school}', [Admin\SchoolController::class, 'destroy'])->name('schools.destroy');
        Route::get('/users', [Admin\UserController::class, 'index'])->name('users');
        Route::get('/moderation', [Admin\ModerationController::class, 'index'])->name('moderation');
        Route::get('/revenue', [Admin\RevenueController::class, 'index'])->name('revenue');
        Route::get('/reports', [Admin\ReportsController::class, 'index'])->name('reports');
    });

    // ── Internal API (JSON) ───────────────────────────────────────────────────
    Route::prefix('api')->group(function () {
        Route::get('/feed', [FeedController::class, 'api'])->name('api.feed');
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('api.notifications.count');
        Route::post('/posts/{post}/react', [ReactionController::class, 'toggle'])->name('api.posts.react');
        Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('api.posts.comments');
        Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('api.posts.comments.store');
        Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('api.polls.vote');
    });
});
