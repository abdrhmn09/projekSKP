<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /* // Komentari semua definisi Gate di sini
        Gate::define('is-admin', function ($user) {
            return $user->role == 'admin';
        });

        Gate::define('is-kepala-sekolah', function ($user) {
            return $user->role == 'kepala_sekolah';
        });

        Gate::define('is-guru', function ($user) {
            return $user->role == 'guru';
        });

        Gate::define('is-staff', function ($user) {
            return $user->role == 'staff';
        });

        Gate::define('is-pegawai', function ($user) {
            return in_array($user->role, ['guru', 'staff']);
        });
        */
    }
} 