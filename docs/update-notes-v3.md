# UPDATING THE PACKAGE to v3.0.0



In v3.0.0 we:

* Introduced support for major changes in Auth0 Laravel SDK v7.8.
* Renamed Laravel's **authentication guard** names - see [Disambiguation of "Authentication Guard" and "Route Middleware Group" names](disambiguation-auth-guard-vs-middleware-group-names.md) in the README file.





## Temporary Upgrade Issues:

While updating project from a version from v2.2.0 onwards, the non-standard 'web_group' and 'api_group' route middleware names in Http/**Kernel.php** cause `composer` to crash... until the new config files have been published.  

If this is an issue, you can temporarily revert these to 'web' and 'api' while performing the upgrade.

```diff
-        'web_group' => [
+       	'web' => [

-        'api_group' => [
+        'api' => [
```



### app/Http/Kernel.php

Undo the temporary renaming of middleware in `app/Http/Kernel.php`  

```diff
protected $middlewareGroups = [
-       	'web' => [
+        'web_group' => [
...
-        'api' => [
+        'api_group' => [
...
];
```

### Auth middleware

In previous version the use of `auth.patched` middleware was neccesary, that is not longer the case. Therefore, remove reference to `auth.patched`

```diff
// This is an example
- ...->middleware('auth.patched:api_guard,web_guard'); //controller constructors using api and web guards
+ ...->middleware('auth:ffm-token-guard,ffm-session-guard'); //controller constructors using api and web guards
```

### .env file:

In v2.x a number of `.env` file settings were required that are redundent and should be removed:
```diff
- AUTH0_AUDIENCE=
- AUTH0_REDIRECT_URI=http://XXXX.com/auth0/callback     // where http://XXXX.com should match your 'APP_URL'
- AUTH0_SCOPE="openid profile email offline_access"
- AUTH0_COOKIE_PATH=/
- # AUTH0_COOKIE_PATH is only required due to bug with auth0/auth0-php (v8.3.6) / auth0/login (v7.2.1).  This hadn't been required v8.3.1 and prior.
```

### New Callback URL

In the Auth0 web dashboard's interface for configuring applications, we need to add new callback route(s) (in **Application URIs → Allowed Callback URLs**) - since the callback route has changed from `xxx.com/auth0/callback` to `xxx.com/callback`

### Blade files

Check if the 'logout-form' contains the `action` attribute, if not included is going to cause error.
This should looks something like this:

```php+HTML
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
```

### Nova

In projects that use Laravel Nova, in  `config/nova.php` revert the reference to the temporary PatchedAuthenticationMiddleware class which is no longer required: 

```diff
-use FaithFM\Auth0Pattern\Http\Middleware\PatchedAuthenticationMiddleware;
+use Laravel\Nova\Http\Middleware\Authenticate;
... 
//Nova Route Middleware
    'api_middleware' => [
        'nova',
-       PatchedAuthenticationMiddleware::class,
+       Authenticate::class,
        Authorize::class,
    ],
```

### Other Packages

In any packages that referenced our legacy `'web_guard'` and  `'api_guard'` guards, these will need to be replaced with our new `'ffm-session-guard'` and  `'ffm-token-guard'` guards.  

One package needing this update is: `owen-it/laravel-auditing`

