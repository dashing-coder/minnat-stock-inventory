<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        $timezone = (string) Setting::valueFor('timezone', config('app.timezone', 'UTC'));
        if (in_array($timezone, timezone_identifiers_list(), true)) {
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        config([
            'erp.currency' => (string) Setting::valueFor('currency', 'USD'),
            'erp.date_format' => (string) Setting::valueFor('date_format', 'Y-m-d'),
        ]);

        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
