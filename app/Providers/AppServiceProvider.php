<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Register policies
        Gate::policy(\App\Models\Book::class, \App\Policies\BookPolicy::class);
        Gate::policy(\App\Models\Borrowing::class, \App\Policies\BorrowingPolicy::class);
        Gate::policy(\App\Models\Booking::class, \App\Policies\BookingPolicy::class);
        Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);
        Gate::policy(\App\Models\Fine::class, \App\Policies\FinePolicy::class);
    }
}
