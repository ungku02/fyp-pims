<?php

namespace App\Providers;

use App\Models\Workspace;
use App\Policies\WorkspacePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Workspace::class => WorkspacePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define a gate for checking if the user is an owner of a workspace
        Gate::define('isOwner', function (User $user) {
            return Workspace::where('user_id', $user->id)->exists();
        });
    }
}
