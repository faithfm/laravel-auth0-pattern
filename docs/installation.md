# How to Install and Update  this Library

## INSTALLING THE PACKAGE

> Note: for our **existing projects** - read [notes for existing projects](docs/installation-existing-project-additional-steps.md) and perform these manual cleanup steps BEFORE+AFTER installation.

Add this library to your project's `composer.json` file:

```json
{
    "require": {
        ...
        "faithfm/laravel-auth0-pattern": "^1.0"
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
```

#### 2. `.env.example` file:

Add (generic Auth0 example details):

```env
AUTH0_DOMAIN=XXXX.au.auth0.com
AUTH0_CLIENT_ID=XXXXXXXXXXXXXXXX
AUTH0_CLIENT_SECRET=XXXXXXXXXXXX
```


## UPDATING THE PACKAGE

```bash
composer update faithfm/laravel-auth0-pattern
php artisan vendor:publish --tag=auth-every-update-force-clones --force
```

