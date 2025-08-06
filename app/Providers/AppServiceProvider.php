<?php

namespace App\Providers;

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
        // Register model observers
        \App\Models\Proposal::observe(\App\Observers\ProposalObserver::class);
        \App\Models\Review::observe(\App\Observers\ReviewObserver::class);
    }
}
