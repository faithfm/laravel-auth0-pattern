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

### Manual Changes:

#### 1. `.env` file:

Add (replacing credentials with your actual Auth0 details):

```env
AUTH0_DOMAIN=XXXX.au.auth0.com
AUTH0_CLIENT_ID=XXXXXXXXXXXXXXXX
AUTH0_CLIENT_SECRET=XXXXXXXXXXXX
AUTH0_AUDIENCE=
AUTH0_REDIRECT_URI=http://XXXX.com/auth0/callback     // where http://XXXX.com should match your 'APP_URL'
AUTH0_COOKIE_PATH=/
# AUTH0_COOKIE_PATH is only required due to bug with auth0/auth0-php (v8.3.6) / auth0/login (v7.2.1).  This hadn't been required v8.3.1 and prior.
```

#### 2. `.env.example` file:

Add (generic Auth0 example details):

```env
AUTH0_DOMAIN=XXXX.au.auth0.com
AUTH0_CLIENT_ID=XXXXXXXXXXXXXXXX
AUTH0_CLIENT_SECRET=XXXXXXXXXXXX
AUTH0_AUDIENCE=
AUTH0_REDIRECT_URI=http://XXXX.com/auth0/callback     // where http://XXXX.com should match your 'APP_URL'
AUTH0_COOKIE_PATH=/
# AUTH0_COOKIE_PATH is only required due to bug with auth0/auth0-php (v8.3.6) / auth0/login (v7.2.1).  This hadn't been required v8.3.1 and prior.
```

#### 3. `App/Http/Kernel.php` file:

Replace Laravel's default *StartSession* middleware with our own:

```php
        'web' => [
            ...
            // \Illuminate\Session\Middleware\StartSession::class,      // replace with...
            \FaithFM\Auth0Pattern\Http\Middleware\StartSession::class,  // ...the class from Auth0Pattern - which doesn't create hundreds of session files when request contains 'api_token=XXXX'
            ...
        ],

```


## UPDATING THE PACKAGE

```bash
composer update faithfm/laravel-auth0-pattern
php artisan vendor:publish --tag=auth-every-update-force-clones --force
```

Note: when upgrading from a lower version to **v1.0.8** (or higher):

* You will need to edit `App/Http/Kernel.php` (as per Installation Step #3 see above)

* If you previously used your own special SessionServiceProvider, you'll need to:

  * Delete files: `app/Http/Middleware/StartSession.php` and `app/Providers/SessionServiceProvider.php`

  * Edit file `config/app.php` and remove the following provider:

```php
        App\Providers\SessionServiceProvider::class,
```
