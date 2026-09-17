<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Assignment;
use App\Observers\AssignmentObserver;

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
        // تسجيل الـ Observer للـ Assignment
        Assignment::observe(AssignmentObserver::class);
    }
}