<?php

namespace App\Providers;

use App\Models\User;
use App\Support\RoleSecurity;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
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
        Gate::before(function (User $user, string $ability) {
            if (RoleSecurity::denied($user, $ability)) return false;
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
