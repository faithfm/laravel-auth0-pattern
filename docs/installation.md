# How to install / configure / update

## Installation:

```bash
composer require faithfm/laravel-auth0-pattern
php artisan vendor:publish --tag=laravel-simple-auth0-migrations
php artisan vendor:publish --tag=laravel-simple-permissions --force
php artisan vendor:publish --tag=laravel-auth0-pattern --force
php artisan migrate
```

See [pattern structure diagram](structure-of-pattern.png) for information about published files and migrations.


## Manual Configuration:

**Route Registration:**

Add the following to ***web.php*** to register the /login, /logout, and /callback routes:

```php
use FaithFM\SimpleAuth0\SimpleAuth0ServiceProvider;

// Register login/logout/callback routes (for Auth0)
SimpleAuth0ServiceProvider::registerLoginLogoutCallbackRoutes();
```



**Token+Session-related Middleware Groups:**

Modify  app/Http/**Kernel.php** to replace the `StartSession` middleware for WEB routes and add the [(optional) session-based middleware](web-middleware-group-for-APIs.md) for API routes:    (applies to **Laravel 10** and earlier)

*(Note: these middeware groups are applied when route files are loaded in app/Providers/**RouteServiceProvider.php***)

```diff
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
-       // \Illuminate\Session\Middleware\StartSession::class,          // replace Laravel default with...
+       \FaithFM\SimpleAuthTokens\Http\Middleware\StartSession::class,  // ...FaithFM\SimpleAuthTokens class - which prevents creation of (numerous) session files for requests containing 'api_token=XXXX'  (ie: clients without support for cookies will normally result in creation of a session-file for every API call - potentially resulting in hundreds/thousands of session-files)
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \App\Http\Middleware\HandleInertiaRequests::class,
    ],
    
    'api' => [
        // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        
+       // OPTIONAL session-related middleware for API routes - recommended by FaithFM\SimpleAuthTokens
+       \App\Http\Middleware\EncryptCookies::class,
+       \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
+       \FaithFM\SimpleAuthTokens\Http\Middleware\StartSession::class,		// FaithFM\SimpleAuthTokens class
+       \Illuminate\View\Middleware\ShareErrorsFromSession::class,
+       \App\Http\Middleware\VerifyCsrfToken::class,
    ],
],
```

For **Laravel 11** onwards, modify bootstrap/**app.php** [instead](laravel-11-bootstrap-app.md)...  [NOT FULLY TESTED]



**Authentication Guard Names:**

By default Laravel (confusingly) re-uses the names 'web' and 'api' to refer to Authentication guard names in config/**auth.php** (as well as middleware groups defined above).  In our published config/**auth.php** we rename these for clarity:

* 'web' --> 'ffm-session-guard'
* 'api' --> 'ffm-token-guard'

If your code has used explicit guard names, you will need to rename them accordingly - ie:

```diff
-  $user = auth('web')->user()
+  $user = auth('ffm-session-guard')->user()

-  $loggedIn = Auth::guard('api')->check();
+  $loggedIn = Auth::guard('ffm-token-guard')->check();

-  ->middleware(['auth:web,api'])
+  ->middleware(['auth:ffm-session-guard,ffm-token-guard'])
```



**RECOMMENDATION:  401 vs redirect to /login:**

Laravel (up to v10) includes a user Class Http/Middleware/**Authenticate.php** whose `redirectTo()` function defaults to redirecting unauthenticated requests to the /login route.  We find it is better to override the `unauthenticated()` method and to an `abort(401)` instead.  This allows us to provide a 401 page handler that includes a login button instead.  

```bash
protected function unauthenticated($request, array $guards)
{
    abort(401, 'You are not logged in.');
}
```

Notes: 

* The laravel-simple-auth0 LoginController has already been configured to try to capture the previous page to allow redirection back to the 'intended' page from the /callback.)

* This recommendation has not been widely deployed across our apps (as-at v4.0.0).



## Updating:

When updating the package (particularly major updates), it is important to re-publish the latest templates, then to re-apply any custom configuration you may require in these publshed templates.

```bash
composer update faithfm/laravel-auth0-pattern
# php artisan vendor:publish --tag=laravel-simple-auth0-migrations    # don't replublish migration
php artisan vendor:publish --tag=laravel-simple-permissions --force
php artisan vendor:publish --tag=laravel-auth0-pattern --force
```



Specific upgrade notes are applicable for the following versions:

* [Upgrading to v1.0.8](update-notes-v1.0.8.md) - apply StartSession middleware
* [Upgrading to v2.2.0](update-notes-v2.2.md) - disambiguate "authentication guard" and "route middleware group" names.
* [Upgrading to v3.0.0](update-notes-v3.md) - support major changes in Auth0 Laravel SDK v7.8, rename "authentication guards".
* [Upgrading to v4.0.0](update-notes-v4.md) - drop Auth0 Laravel SDK, refactor into 3x sub-packages, revert "route middleware group" names.

