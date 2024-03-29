<?php

namespace FaithFM\Auth0Pattern;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Repositories\AuthPermissionList;
use FaithFM\Auth0Pattern\Http\Middleware\PatchedAuthenticationMiddleware;
use Illuminate\Routing\Router;

class Auth0PatternServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register() :void
    {
        $this->app->bind(
            \Auth0\Login\Contract\Auth0UserRepository::class,
            \FaithFM\Auth0Pattern\Auth0PatternUserRepository::class,
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(Router $router): void
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

        // Create gates for each permission an application defines in the AuthPermissionList
        if (class_exists(AuthPermissionList::class)) {
            foreach (AuthPermissionList::DEFINED_PERMISSIONS as $permission) {
                Gate::define($permission, function ($user) use ($permission) {
                    return $user->permissions->firstWhere('permission', $permission) !== null;     // check if the specified permission exists in the current User's UserPermissions model
                });
            }
        }

        // Register our Authentication Middleware
        $router->aliasMiddleware('auth.patched', PatchedAuthenticationMiddleware::class);

        // Create routes
        $this->loadRoutesFrom(__DIR__.'/routes/auth0pattern-web.php');

        // Load database migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
