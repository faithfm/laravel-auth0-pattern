<?php

namespace FaithFM\AuthLaravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Repositories\AuthPermissionsRepository;

class AuthLaravelServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \Auth0\Login\Contract\Auth0UserRepository::class,
            \App\Repositories\CustomUserRepository::class,
        );
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // (ONCE-OFF ON PACKAGE INSTALLATION)
        // Publish everything contained in the "templates" folder
        //   > php artisan vendor:publish
        $this->publishes([
            __DIR__.'/../templates/' => base_path(),
        ], 'auth-once-off-installation');

        // (EVERY TIME THE PACKAGE IS INSTALLED/UPDATED)
        // Publish everything contained in the "clone" folder:
        //   > php artisan vendor:publish --tag=auth-force-updates --force
        $this->publishes([
            __DIR__.'/../clone/' => base_path(),
        ], 'auth-every-update-force-clones');


        // Create gates for each permission an application defines in the AuthPermissionsRepository
        if (class_exists(AuthPermissionsRepository::class)) {
            foreach (AuthPermissionsRepository::DEFINED_PERMISSIONS as $permission) {
                Gate::define($permission, function ($user) use ($permission) {
                    return $user->permissions->firstWhere('permission', $permission) !== null;     // check if the specified permission exists in the current User's UserPermissions model
                });
            }
        }

        // Create routes
        $this->loadRoutesFrom(__DIR__.'/routes');

        // Load database migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
