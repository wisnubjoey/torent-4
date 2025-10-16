<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class InertiaServiceProvider extends ServiceProvider
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
        Inertia::share('layout', 'Layouts/AppLayout');

        Inertia::share('app', fn () => [
            'name' => config('app.name'),
        ]);

        Inertia::share('auth', fn () => [
            'user' => Auth::user(),
        ]);

        Inertia::share('flash', fn () => [
            'message' => Session::get('message'),
            'status' => Session::get('status'),
        ]);
    }
}
