<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Question;
use App\Policies\CommentPolicy;
use App\Policies\QuestionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Question::class, QuestionPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
    }
}
