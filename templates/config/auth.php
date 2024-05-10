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
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'ffm-session-guard',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Our faithfm/laravel-auth0-pattern package renames the default Laravel guards 
    | for clarity, searchability, and disambiguation with route middleware naming:
    |   * 'web' guard is renamed to 'ffm-session-guard'
    |   * 'api' guard is renamed to 'ffm-token-guard'
    |
    */

    'guards' => [
        // Renamed, but identical to Laravel's default 'web' session guard
        // (The actual login is performed by CallbackController in laravel-simple-auth0)
        'ffm-session-guard' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Renamed, but identical to Laravel's default 'api' token guard 
        // (Still supported although config not included by default in new projects since Laravel 7)
        // (Database migrations laravel-simple-auth-tokens package and User model changes documented in the )
        'ffm-token-guard' => [
            'driver' => 'token',
            'provider' => 'users',
            'input_key' => 'api_token',       // Default value - shown here for clarity
            'storage_key' => 'api_token',     // Default value - shown here for clarity
            'hash' => false,                  // Default value - shown here for clarity
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        // Laravel's default user-provider configuration
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    | CURRENTLY UNUSED in Faith FM pattern (Auth0 is responsible for password reset functionality)
    |
    */

    // Laravel's default password reset configuration (NOT IN USE)
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
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
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    | CURRENTLY UNUSED in Faith FM pattern (we use Auth0's password reset functionality)
    | 
    */

    // Laravel's default password confirmation timeout (NOT IN USE)
    'password_timeout' => 10800,

    /*
    |--------------------------------------------------------------------------
    | Authorization Permissions List   (Faith FM laravel-auth0-pattern)
    |--------------------------------------------------------------------------
    |
    | The list of authorization permissions recognised by the application 
    | (as applied to each user in the 'user_permissions' table).
    |
    | Faith FM laravel-simple-permissions automatically creates Gates for all permissions 
    | defined here.  (See: SimplePermissionsServiceProvider.php)
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
