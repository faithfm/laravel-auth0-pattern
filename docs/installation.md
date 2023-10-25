# How to Install and Update  this Library

## INSTALLING THE PACKAGE

> Note: for our **existing projects** - read [notes for existing projects](docs/installation-existing-project-additional-steps.md) and perform these manual cleanup steps BEFORE+AFTER installation.

Add this library to your project's `composer.json` file:

```json
{
    "require": {
        ...
        "faithfm/laravel-auth0-pattern": "^2.0"
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
(If you are using homestead run this commands inside homestead ssh)

```bash
composer update faithfm/laravel-auth0-pattern
php artisan vendor:publish --provider="FaithFM\Auth0Pattern\Auth0PatternServiceProvider" --force
php artisan vendor:publish --provider="Auth0\Login\LoginServiceProvider" --force
php artisan migrate       # 'user_permissions' table skipped if already exists
npm run prod              # or 'npm run watch'
```

### Manual Changes

1. Create app in Auth0 interface to get credential used in next step

2. `.env` file

   Add (replacing credentials with your actual Auth0 details):

   ```env
   AUTH0_DOMAIN=XXXX.au.auth0.com
   AUTH0_CLIENT_ID=XXXXXXXXXXXXXXXX
   AUTH0_CLIENT_SECRET=XXXXXXXXXXXX
   AUTH0_AUDIENCE=
   AUTH0_REDIRECT_URI=http://XXXX.com/auth0/callback     // where http://XXXX.com should match your 'APP_URL'
   AUTH0_SCOPE="openid profile email offline_access"
   AUTH0_COOKIE_PATH=/
   # AUTH0_COOKIE_PATH is only required due to bug with auth0/auth0-php (v8.3.6) / auth0/login (v7.2.1).  This hadn't been required v8.3.1 and prior.
   ```

3. `.env.example` file

   Add (generic Auth0 example details):

   ```env
   AUTH0_DOMAIN=XXXX.au.auth0.com
   AUTH0_CLIENT_ID=XXXXXXXXXXXXXXXX
   AUTH0_CLIENT_SECRET=XXXXXXXXXXXX
   AUTH0_AUDIENCE=
   AUTH0_REDIRECT_URI=http://XXXX.com/auth0/callback     // where http://XXXX.com should match your 'APP_URL'
   AUTH0_SCOPE="openid profile email offline_access"
   AUTH0_COOKIE_PATH=/
   # AUTH0_COOKIE_PATH is only required due to bug with auth0/auth0-php (v8.3.6) / auth0/login (v7.2.1).  This hadn't been required v8.3.1 and prior.
   ```

4. `App/Http/Kernel.php` file

   Replace Laravel's default *StartSession* middleware with our own:

   ```php
           'web' => [
               ...
               // \Illuminate\Session\Middleware\StartSession::class,      // replace with...
               \FaithFM\Auth0Pattern\Http\Middleware\StartSession::class,  // ...the class from Auth0Pattern - which doesn't create hundreds of session files when request contains 'api_token=XXXX'
               ...
           ],
   
   ```

   * Rename middleware groups in `app/Http/Kernel.php` to match guard name created in our project

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

5. Rename middleware in `app/Providers/RouteServiceProvider.php`

   (Note: Sometimes the order of functions has been swapped, just make sure to change the `middleware` function)

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
   + ...->middleware('auth.patched:api_guard,web_guard'); //controller constructors using api and web guards
   ```

   

## UPDATING THE PACKAGE

```bash
composer update faithfm/laravel-auth0-pattern
php artisan vendor:publish --tag=auth-every-update-force-clones --force
```

Note: when upgrading from a lower version to **v1.0.8** (or higher):

* You will need to edit `App/Http/Kernel.php` (as per Installation Step #4 see above)

* If you previously used your own special SessionServiceProvider, you'll need to:

  * Delete files: `app/Http/Middleware/StartSession.php` and `app/Providers/SessionServiceProvider.php`

  * Edit file `config/app.php` and remove the following provider:

```php
        App\Providers\SessionServiceProvider::class,
```

Note: when upgrading from a lower version to **v2.2.0** (or higher):

* You will need to rename middleware in `app/Providers/RouteServiceProvider.php` (as per Installation Step #5 see above)
* Also is neccesary to change ocurrence when middleware **auth** is used (as per Installation Step #6 see above)
