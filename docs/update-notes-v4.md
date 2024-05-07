# UPDATING THE PACKAGE to v4.0.0

In v4.0.0 we:

* Moved away from the official `auth0/login` (Auth0 Laravel SDK) in favour of our own simpler auth0 package.  Login/logout/callback route registration is now manually added to routes/**web.php**.
* Split the functionality of the package into 3x child packages which are now dependencies of this package.
* Created a proper packagist compose package `faithfm/laravel-auth0-pattern` (no need for composer VCS references).
* Reverted **route middleware group** names back to Laravel defaults - see [Disambiguation of "Authentication Guard" and "Route Middleware Group" names](disambiguation-auth-guard-vs-middleware-group-names.md).
* Moved the addition of session-based API middleware from app/Providers/**RouteServiceProvider.php** to app/Http/**Kernel.php**.
* Renamed database migrations when the packages were split.  Manual editing of the *'migrations'* table is required.



## Changes Required:

### Composer VCS Requirement Dropped:

Remove the VCS package definition from `composer.json`:

```diff
"repositories": [
-    {
-        "type": "vcs",
-            "url": "https://github.com/faithfm/laravel-auth0-pattern.git"
-    },
```

Uninstall then re-install the package to load it from packagist:

```bash
composer remove faithfm/laravel-auth0-pattern
composer require faithfm/laravel-auth0-pattern
php artisan vendor:publish --tag=laravel-simple-permissions --force
php artisan vendor:publish --tag=laravel-auth0-pattern --force
```

### Migrating from v2
Be sure to update:
* The [Auth Middleware](https://github.com/faithfm/laravel-auth0-pattern/blob/lidiaordonez-patch-1/docs/update-notes-v3.md#auth-middleware)
* The [.env file](https://github.com/faithfm/laravel-auth0-pattern/blob/lidiaordonez-patch-1/docs/update-notes-v3.md#env-file)
* The [new callback url](https://github.com/faithfm/laravel-auth0-pattern/blob/lidiaordonez-patch-1/docs/update-notes-v3.md#new-callback-url)
* The change in the [blade file](https://github.com/faithfm/laravel-auth0-pattern/blob/lidiaordonez-patch-1/docs/update-notes-v3.md#blade-files)
* If you are using Nova, this [change](https://github.com/faithfm/laravel-auth0-pattern/blob/lidiaordonez-patch-1/docs/update-notes-v3.md#nova) is needed
  

### Login/logout/callback Route Registration:

Add the following to ***web.php*** to register the /login, /logout, and /callback routes:

```php
use FaithFM\SimpleAuth0\SimpleAuth0ServiceProvider;

...

// Register login/logout/callback routes (for Auth0)
SimpleAuth0ServiceProvider::registerLoginLogoutCallbackRoutes();
```



### Route Middleware Providers:

Revert the dual-web+api middleware groups in app/Providers/**RouteServiceProvider.php**:

*(Note: slightly different syntax for Laravel 8 vs 9)*

```diff
public function boot()

// FOR LARAVEL 8
        Route::prefix('web')
-            ->middleware('web_guard')
+            ->middleware('web')
            ->group(base_path('routes/web.php'));
...
        Route::prefix('api')
-            ->middleware(['web_guard', 'api_guard'])
+            ->middleware('api')
            ->group(base_path('routes/api.php'));


// FOR LARAVEL 9 + 10
-       Route::middleware('web_guard')
+       Route::middleware('web')
            ->prefix('web')
            ->group(base_path('routes/web.php'));
...
-       Route::middleware(['web_guard', 'api_guard'])
+       Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));
```



### Route Middeware Definitions:

Replace the *web* + *api* route middleware definitions in app/Http/**Kernel.php** with the updated definitions from the installation instructions:  (Revert naming of 'web_group' + 'api_group', use StartSession from SimpleAuthTokens, and add session-middleware to *api* middleware)

```diff
protected $middlewareGroups = [
-   'web_guard' => [
+   'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        // \Illuminate\Session\Middleware\StartSession::class,          // replace Laravel default with...
-       \FaithFM\Auth0Pattern\Http\Middleware\StartSession::class,
+       \FaithFM\SimpleAuthTokens\Http\Middleware\StartSession::class,  // ...FaithFM\SimpleAuthTokens class - which prevents creation of (numerous) session files for requests containing 'api_token=XXXX'  (ie: clients without support for cookies will normally result in creation of a session-file for every API call - potentially resulting in hundreds/thousands of session-files)
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \App\Http\Middleware\HandleInertiaRequests::class,
    ],
    
-   'api_guard' => [
+   'api' => [
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



### laravel/nova

Revert the middleware-group name in `config/nova.php`:

```diff
... 
//Nova Route Middleware
'middleware' => [
-       'web_group',
+       'web',
        HandleInertiaRequests::class,
        DispatchServingNovaEvent::class,
        BootTools::class,
    ],
```



### binarytorch/larecipe

Revert middleware-group names in `config/larecipe.php` *(if applicable)*:

```diff
//Documentation Routes

'docs' => [
        'route' => '/docs',
        'path' => '/resources/docs',
        'landing' => 'sched-editor-colour-schemes',
-       'middleware' => ['web_group'],
+       'middleware' => ['web'],
    ],
   ...
   'settings' => [
        'auth' => false,
        'guard' => null,
        'ga_id' => '',
        'middleware' => [
-           'web_group',
+           'web',
        ],

```



### Manually Patch Database Migrations:

Manually modify the *'migrations'* database table (to prevent double-up migrations):

```diff
Replace these rows:
-  2021_11_02_231849_edit_users_table 

With these ones:
+  XXXX_XX_XX_XXXXXX_edit_users_table_auth0_changes
+  2024_04_30_000000_edit_users_table_api_token
```



Test valid migration status with:

```bash
php artisan migrate:status
```

