<?php

namespace App\Providers;

use App\Models\{Post, Group, Event, Comment, Reaction};
use App\Policies\{PostPolicy, GroupPolicy, EventPolicy, CommentPolicy};
use App\Observers\{PostObserver, CommentObserver, ReactionObserver};
use App\View\Components\{AppLayout, GuestLayout, AdminLayout};
use Illuminate\Support\Facades\{Blade, Gate};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Component aliases used throughout all Blade views
        Blade::component('layouts.app',   AppLayout::class);
        Blade::component('layouts.guest', GuestLayout::class);
        Blade::component('layouts.admin', AdminLayout::class);

        Gate::policy(Post::class,    PostPolicy::class);
        Gate::policy(Group::class,   GroupPolicy::class);
        Gate::policy(Event::class,   EventPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);

        Post::observe(PostObserver::class);
        Comment::observe(CommentObserver::class);
        Reaction::observe(ReactionObserver::class);
    }
}
