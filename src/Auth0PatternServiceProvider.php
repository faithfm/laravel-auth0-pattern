<?php

namespace FaithFM\Auth0Pattern;

use App\Models\User;
use FaithFM\Auth0Pattern\Guards\PatchedAuthenticationGuard;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;

class Auth0PatternServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register() :void
    {
        $this->app->bind(
            \FaithFM\Auth0Pattern\Auth0PatternUserRepository::class,
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(Router $router, AuthManager $auth): void
    {
        // Publish everything contained in the "templates" folder
        //   > php artisan vendor:publish --tag=laravel-auth0-pattern --force
        $this->publishes([
            __DIR__.'/../templates/' => base_path(),
        ], 'laravel-auth0-pattern');

        // Instead of creating gates for each permission defined in config/auth.php, we create a single gate that checks for multiple permissions
        // This gate allows us to use the '|' character to check for multiple (ORed) permissions in a single gate
        Gate::after(function (User $user, string $ability, bool|null $result, mixed $arguments) {
            // Explode the ability into parts - ie: 'edit-post|delete-post' -> ['edit-post', 'delete-post']
            $abilities = explode('|', $ability);

            // Ensure specified abilities match the 'defined_abilities' in config/auth.php
            $abilities = Arr::map($abilities, function (string $ability) {
                $ability = trim($ability);
                if (!in_array($ability, config('auth.defined_permissions', []))) {
                    throw new \Exception("The specified ability '$ability' is not a 'defined_permission' in config/auth.php");
                }
                return $ability;
            });

            // Check if the user has any of the allowed abilities
            foreach ($abilities as $ability) {
                if ($user->permissions->firstWhere('permission', $ability) !== null) {
                    return true;
                }
            }
        });            

        // Register our patched Authentication Guard driver
        // Note: this driver is a temporary bug-fix to overcome a current (v7.12) bug where the Auth0 SDK does not correctly handle the 'accessToken' vs 'idToken' when AUTH0_AUDIENCE is blank
        $auth->extend('auth0.authenticator.patched', static fn (Application $app, string $name, array $config): PatchedAuthenticationGuard => new PatchedAuthenticationGuard($name, $config));

        // Register routes   (unless disabled in the config file)
        if (true === config('auth0.ffmRegisterAuthenticationRoutes')) {
            $this->loadRoutesFrom(__DIR__.'/routes/auth0pattern-web.php');
        }

        // Load database migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

    }
}
