<?php

/**
 * This file is cloned / force-published from the "laravel-auth0-pattern" composer package.
 *    WARNING: Local modifications will be overwritten when the package is updated.
 *             See https://github.com/faithfm/laravel-auth0-pattern for more details.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. 
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'ffm-session-guard'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application.
    |
    */

    'guards' => [
        // Faith FM's session guard (Auth0 session driver + an Eloquent-linked user-provider)
        'ffm-session-guard' => [
          'driver' => 'auth0.authenticator.patched',    // patched to fix the issue with accessToken vs idToken when AUTH0_AUDIENCE is blank (ie: using the default https://faithfm.au.auth0.com/userinfo endpoint instead of an API endpoint)
          'provider' => 'ffm-auth0-user-provider',      // based on our custom User Repository
          'configuration' => 'web',                     // not documented well, but this setting points Auth0's configurator to use the 'guards.web' section defined in 'config/auth0.php' - see config('auth0.guards.' . $this->guardConfigurationKey) in InstanceEntityAbstract.php
        ],

        // // Auth0's default session guard (does not implement an Eloquent-linked user provider) - NOT NORMALLY USED IN OUR APPS
        // 'auth0-session' => [
        //   'driver' => 'auth0.authenticator',
        //   'provider' => 'auth0-provider',
        //   'configuration' => 'web',
        // ],

        // Faith FM's token-based guard (same as Larave's default token-based guard - renamed from 'api' to 'ffm-token-guard' to avoid confusion + increase searchability
        'ffm-token-guard' => [
            'driver' => 'token',
            'provider' => 'eloquent_users',
            'hash' => false,
        ],

        // // Auth0's default token guard - NOT NORMALLY USED IN OUR APPS
        // 'auth0-api' => [
        //     'driver' => 'auth0.authorizer',
        //     'configuration' => 'api',
        //     'provider' => 'auth0-provider',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved out of your database or other storage
    | system used by the application. 
    |
    */

    'providers' => [
        // Faith FM's user-provider (including Auth0 information + Eloquent User model information, by means of our Auth0PatternUserRepository)
        'ffm-auth0-user-provider' => [
            'driver' => 'auth0.provider',
            'repository' => FaithFM\Auth0Pattern\Auth0PatternUserRepository::class,
        ],

        // // Auth0's default user-provider (when Auth0 information only is required - ie: when not using an Eloquent User model) - NOT NORMALLY USED IN OUR APPS
        // 'auth0-provider' => [
        //   'driver' => 'auth0.provider',
        //   'repository' => 'auth0.repository',
        // ],

        // Laravel's default user-provider - used by our 'ffm-token-guard' guard
        'eloquent_users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table utilized for token storage
    | and the user provider that is invoked to actually retrieve users.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    | CURRENTLY UNUSED in Faith FM pattern (we use Auth0's password reset functionality)
    |
    */

    // Laravel's default password reset configuration (NOT IN USE)
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    | CURRENTLY UNUSED in Faith FM pattern (we use Auth0's password reset functionality)
    | 
    */

    // Laravel's default password confirmation timeout (NOT IN USE)
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

    /*
    |--------------------------------------------------------------------------
    | Authorization Permissions List   (Faith FM laravel-auth0-pattern)
    |--------------------------------------------------------------------------
    |
    | The list of authorization permissions recognised by the application 
    | (as applied to each user in the 'user_permissions' table).
    |
    | Faith FM laravel-auth0-pattern automatically creates Gates for all permissions 
    | defined here.  (See: Auth0PatternServiceProvider.php)
    |
    | Ie: permissions applied in the 'user_permissions' table must be defined here
    | before they will be available for Authorization in the application.
    |
    | Gates can be used in controllers, views, etc - ie: Gate::allows('edit-catalog')
    | For more information see README.md and https://laravel.com/docs/master/authorization
    | 
    | Our pattern allows for multiple permissions to be checked in a single gate using the '|' character.
    | Ie: Gate::allows('view-catalog|edit-catalog').  This works in middleware 'can' checks as well.
    | 
    */

    'defined_permissions' => [
        'use-app',                  // minimum permission to use the app
        'admin-app',                // master admin privilege
    //  'edit-catalog',             // for catalog editors  (assuming you're writing a catalogue application)
    ],

];
