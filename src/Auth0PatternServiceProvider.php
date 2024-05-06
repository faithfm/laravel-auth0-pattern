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
     * Bootstrap any package services.
     */
    public function boot(Router $router, AuthManager $auth): void
    {
        // Publish everything contained in the "templates" folder
        //   > php artisan vendor:publish --tag=laravel-auth0-pattern --force
        $this->publishes([
            __DIR__.'/../templates/' => base_path(),
        ], 'laravel-auth0-pattern');
    }
}
