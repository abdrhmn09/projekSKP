<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Gate::define('is-admin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('is-kepala-sekolah', function (User $user) {
            return $user->role === 'kepala_sekolah';
        });

        Gate::define('is-guru', function (User $user) {
            return $user->role === 'guru';
        });

        Gate::define('is-staff', function (User $user) {
            return $user->role === 'staff';
        });

        Gate::define('is-pegawai', function (User $user) {
            return in_array($user->role, ['guru', 'staff']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
