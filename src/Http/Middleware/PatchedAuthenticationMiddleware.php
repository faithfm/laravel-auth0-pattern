<?php

namespace FaithFM\Auth0Pattern\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as LaravelAuthenticateMiddleware;
use Auth0\Laravel\Auth\Guard as Auth0Guard;
use Auth0\Laravel\Contract\Entities\Credential as Auth0Credential;

/**
 * Class PatchedAuthenticationMiddleware
 *
 * Registered as the 'auth.patched' middleware in app/Http/Kernel.php
 *
 * Usage:  (similiar to Laravel's 'auth:XXX,YYY' middleware)
 *    middleware('auth.patched)               // Check authentication using the default guard
 *    middleware('auth.patched:XXX')          // Check authentication using the 'XXX'' guard
 *    middleware('auth.patched:XXX,YYY')      // Check authentication using the 'XXX' OR the 'YYY' guard
 *
 * This class is a temporary workaround for Auth0's broken support for Laravel's 'auth' middleware (as-at v7.6 / Apr 2023).
 * See: https://github.com/auth0/laravel-auth0/issues/384
 *
 * We are extending Laravel's 'auth' middleware (Illuminate/Auth/Middleware/Authenticate.php) which allows multiple (OR-ed) guards to be specified.
 * We have added additional logic to load Auth0 Credentials from the Session - since auth0/login's check() method does not do this.
 *
 * @package App\Http\Middleware
 */
class PatchedAuthenticationMiddleware extends LaravelAuthenticateMiddleware
{
    /**
     * Determine if the user is logged in to any of the given guards.
     * NOTE: Overriding Laravel's default one XXXXXXXX
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guardName) {
            $guard = $this->auth->guard($guardName);

            if ($guard instanceof Auth0Guard) {
                // Hack to compensate for auth0/login v7.6's broken support for Laravel's 'auth' middleware. (See: https://github.com/auth0/laravel-auth0/issues/384)
                // Concept taken from logic found in auth0/login / Http/Controller/Stateful/Login.php
                $loggedIn = $guard->check() ? true : $guard->find(Auth0Guard::SOURCE_SESSION) instanceof Auth0Credential;
            } else {
                $loggedIn = $guard->check();
            }

            if ($loggedIn) {
                return $this->auth->shouldUse($guardName);
            }
        }

        $this->unauthenticated($request, $guards);
    }
}
