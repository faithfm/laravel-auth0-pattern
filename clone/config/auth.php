<?php

/**
 * This file is cloned / force-published from the "laravel-auth0-pattern" composer package.
 *    WARNING: Local modifications will be overwritten when the package is updated.
 *             See https://github.com/faithfm/laravel-auth0-pattern for more details.
 */


 /** 
 Note: auth0/login v7's implementation is now very reliant on the specific naming of guard + driver + provider = 'auth0'.
     (This can be a little confusing to understand/debug since each performs a different function, yet all three are identically named.)
     
    This later led to other issues that have been temporarily 'fixed' by creating duplicate 'web' + 'auth0' guards to keep both Laravel and Auth0 happy  (see below)

    EXPLANATION
    The specific config('auth.guards.auth0.provider') that has been coded into 'auth0/login' src/Auth/Guard.php prevents us from naming our default guard 'web' 
    using an 'auth0' driver... (our preferred approach in laravel-auth0-pattern v1.x).

    ...but if we try to use 'auth0.authenticate' or 'auth0.authenticate.default' guards...
    (as per suggestions in the QuickStart: https://auth0.com/docs/quickstart/webapp/laravel#protecting-routes)

    ...then we experience side-effects/errors when the default 'web' guard is not used in the middleware.
    (see others experiencing same issue here: https://community.auth0.com/t/implementing-auth0-in-addition-to-existing-laravel-auth/91225)

    One solution would be to fork our own version of the 'auth0/login' repo that looks up the actual provider (rather than assuming specific hard-coded naming), however 'auth0/login' is currently unstable and somewhat difficult to debug, and we found it easier to "cheat" by creating duplicate 'web' + 'auth0' guards in config/auth.php - which:
    1. Keeps Laravel happy by allowing us to use the default 'web' guards in our middleware
    2. Keeps 'auth0/login' hard-coded settings happy because the duplicate 'auth0' guard exists in config/auth.php.

    Note: 
    The actual error is caused when we try to use the 'auth0.authenticate' guard instead of the 'web' guard for our API routes middleware (in RouteServiceProvider.php).  

    It seemed to work ok for actual web routes, but didn't work properly as an additional guard for API routes.  We weren't able to get to the bottom of the issue, but an exception is raised when a null user is returned in some circumstances because it seems to not be able to retrieve a stateful user - see code sections:
    - src/Auth/Guard.php functions: user(), getUserFromSession()
    - src/Http/Middleware/Stateful/AuthenticateOptional.php functions: handle()

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
        'guard' => 'auth0',
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
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'auth0' => [
            'driver' => 'auth0',
            'provider' => 'auth0',
        ],

        // duplicate of 'auth0' guard required as temporary bug-fix - see notes at top of file:
        'web' => [
            'driver' => 'auth0',
            'provider' => 'auth0',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'eloquent_users',
            'hash' => false,
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
        'auth0' => [
            'driver' => 'auth0',
            'repository' => FaithFM\Auth0Pattern\Auth0PatternUserRepository::class,

        ],
        'eloquent_users' => [
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
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
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
    */

    'password_timeout' => 10800,

];
