# Session-based ('web') Middleware for API routes:

For many of our projects, we want to enable ***session***-based (as well as *token*-based) authentication in our API routes - ie:

```diff
-  ...->middleware('auth')
+  ...->middleware('auth:ffm-session-guard,ffm-token-guard')
```



To enable this we add session-related middleware to the ***api* middleware-group** in app/Http/**Kernel.php**:

```diff
protected $middlewareGroups = [
    'api' => [
        // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        
+				// Additional session-related middleware
+       \App\Http\Middleware\EncryptCookies::class,
+       \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
+       \FaithFM\SimpleAuthTokens\Http\Middleware\StartSession::class,
+       \Illuminate\View\Middleware\ShareErrorsFromSession::class,
+       \App\Http\Middleware\VerifyCsrfToken::class,
    ],
];
```



### Explanation:

* Route Definitions:
  * **API** routes (/api) are defined in routes/**api.php** 
  * **Web** routes are defined in routes/**web.php** 
* Both of these routes are loaded by app/Providers/**RouteServiceProvider.php**... which normally applies the following middleware-groups to each set of routes:
  * routes/**web.php** = *'web'* middleware-group
  * routes/**api.php** = *'api'* middleware-group
* These middleware-groups are defined in app/Http/**Kernel.php**:

```php
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
```

* The *'api'* middleware-group does not normally include any of the session-related middleware (ie: *StartSession*, *VerifyCsrfToken*, etc) that are defined for the *'web'* middleware-group.
* Adding these to the *'api'* middleware-group enables session-based guards to work for our */api* routes.



## ~~Superceded / Legacy:~~

Note: instead of adding session-related middleware to the 'api' middleware-group in Kernel.php, ***earlier versions*** of our pattern left the 'api' middleware-group definitions untouched in app/Http/**Kernel.php**, and **instead** applied both (**'web'** AND **'api'**) middleware-groups when loading API routes in app/Providers/**RouteServiceProvider.php**:

```diff
public function boot()

// FOR LARAVEL 8
        Route::prefix('api')
-            ->middleware('api')
+            ->middleware(['web', 'api'])
            ->group(base_path('routes/api.php'));

// FOR LARAVEL 9 + 10
-       Route::middleware('api')
+       Route::middleware(['web', 'api'])
            ->prefix('api')
            ->group(base_path('routes/api.php'));
```

We changed our approach (in v4.0.0) to the one documented above - for better visibility, and for better forward-compatibility with Laravel 11.


