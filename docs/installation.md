# How to Install and Update  this Library

## INSTALLING THE PACKAGE

Add this library to your project's `composer.json` file:

```json
{
    "require": {
        ...
        "faithfm/laravel-auth0-pattern": "^3.0"
    }
    ...

    "repositories": {
        "laravel-auth0-pattern": {
            "type": "vcs",
            "url": "https://github.com/faithfm/laravel-auth0-pattern"
        }
    }
}
```

...then install using the following commands:

```bash
composer update faithfm/laravel-auth0-pattern
php artisan vendor:publish --tag=auth0 --force
php artisan vendor:publish --tag=laravel-auth0-pattern --force
php artisan migrate       # 'user_permissions' table skipped if already exists
```

### Manual Changes

1. Create an app in the Auth0 web interface to provide credentials used in next step

2. `.env` file

   Add (replacing credentials with your actual Auth0 details):

   ```env
   AUTH0_DOMAIN=XXXX.au.auth0.com
   AUTH0_CLIENT_ID=XXXXXXXXXXXXXXXX
   AUTH0_CLIENT_SECRET=XXXXXXXXXXXX
   ```
   
3. `.env.example` file

   Add (generic Auth0 example details):

   ```env
   AUTH0_DOMAIN=XXXX.au.auth0.com
   AUTH0_CLIENT_ID=XXXXXXXXXXXXXXXX
   AUTH0_CLIENT_SECRET=XXXXXXXXXXXX
   ```
   
4. `App/Http/Kernel.php` file

   Replace Laravel's default *StartSession* middleware with our own:  (to prevent hundreds of session files)

      ```php
      'web' => [
          ...
          // \Illuminate\Session\Middleware\StartSession::class,      // replace with...
          \FaithFM\Auth0Pattern\Http\Middleware\StartSession::class,  // ...the class from Auth0Pattern - which prevents creation of (numerous) session files for requests containing 'api_token=XXXX'  (ie: clients without support for cookies will normally result in creation of a session-file for every API call - potentially resulting in hundreds/thousands of session-files)
          ...
      ],
      
      ```
   
   Rename middleware groups in `app/Http/Kernel.php`
   
    > [!NOTE]
    >
    > This was made to provide much greater clarity/visibility/searchability when understanding middleware groups vs route definitions vs auth guards, etc
    >
    > The automatic registration + templates in this library/pattern relies on these changes
   
   
   ```diff
       protected $middlewareGroups = [
   -       'web' => [
   +       'web_group' => [
               ...
           ]
   -       'api' => [
   +       'api_group' => [
               ...
           ]
   ```
   


5. Rename middleware in `app/Providers/RouteServiceProvider.php` (to match)

   > [!NOTE]
   >
   >  Sometimes the order of functions has been swapped, just make sure to change the `middleware` function

   ```diff
       protected function mapWebRoutes(): void
       {
   -       Route::middleware('web')
   +       Route::middleware('web_group')
               ->group(base_path('routes/web.php'));
       }
       ...
       protected function mapApiRoutes(): void
       {
           Route::prefix('api')
   -           ->middleware(['web', 'api'])         // add non-standard 'web' middleware option for API routes too - to allow authentication using session cookies instead of api_token etc
   +           ->middleware(['web_group', 'api_group'])         // add non-standard 'web' middleware option for API routes too - to allow authentication using session cookies instead of api_token etc
               ->group(base_path('routes/api.php'));
       }    
   ```

6. Change ocurrence when middleware **auth** is used. Ie:

   ```diff
   - ...->middleware(['auth']);
   + ...->middleware('auth:ffm-token-guard,ffm-session-guard'); //ie: controller constructors using api and web guards
   ```
   > [!NOTE]
   >
   >  In v2.0 we used auth.patched, that is not longer needed
   
   

## UPDATING THE PACKAGE V2 → V3

To avoid the following error while updating version:  (due to auth0/login v7.12 package installation exception without 'web' middleware)

```bash
 InvalidArgumentException:

  The [web] middleware group has not been defined.
```

Rename middleware in `app/Http/Kernel.php`  (temporarily)

```diff
protected $middlewareGroups = [
-        'web_group' => [
+       	'web' => [
...

-        'api_group' => [
+        'api' => [
...
];
```

In the `composer.json` file change dependency version to accept version 3

```
        "faithfm/laravel-auth0-pattern": "^3.0",
```

```bash
composer update faithfm/laravel-auth0-pattern -W
php artisan vendor:publish --tag=laravel-auth0-pattern --force
```

### `app/Http/Kernel.php`

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

## Other version updates instructions

> [!NOTE]
>
> When upgrading from a lower version to **v1.0.8** (or higher):

* You will need to edit `App/Http/Kernel.php` (as per Installation Step #4 see above)

* If you previously used your own special SessionServiceProvider, you'll need to:

  * Delete files: `app/Http/Middleware/StartSession.php` and `app/Providers/SessionServiceProvider.php`

  * Edit file `config/app.php` and remove the following provider:

```php
        App\Providers\SessionServiceProvider::class,
```

> [!NOTE]
>
> When upgrading from a lower version to **v2.2.0** (or higher):

* You will need to rename middleware in `app/Providers/RouteServiceProvider.php` (as per Installation Step #5 see above)
* Also is neccesary to change ocurrence when middleware **auth** is used (as per Installation Step #6 see above)

