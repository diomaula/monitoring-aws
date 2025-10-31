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

        //
        Gate::define('superadmin', function ($user) {
            return $user->role === 'superadmin';
        });
        Gate::define('forecast', function ($user) {
            return $user->role === 'forecast';
        });
        Gate::define('teknisi', function ($user) {
            return $user->role === 'teknisi';
        });
    }
}
