# auth-laravel-v1

Laravel AuthN+AuthZ (for Faith FM projects):

* AuthN (Authentication) implemented using Auth0
* AuthZ (Authorization)  implemented using local user-permissions table (with Laravel/Vue-JS helpers)

This repo is a composer package created to improve consistency across our existing Faith FM Laravel+Vue projects.  (Previously we had been trying to maintain multiple copies of these files across multiple projects).

At present, Laravel Artisan's vendor-publishing functionality is simply being used to clone a set of consistent files across our projects.

## Installation

Add the following to your project's `composer.json` file:

```json
{
    ...
    "require": {
        ...
        "faithfm/auth-laravel-v1": "^1.0"
    }
    ...
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/faithfm/auth-laravel-v1"
        }
    ]
    ...
}
```

...then install using the following commands:
(If you are using homestead run this commands inside homestead ssh)

```bash
composer update faithfm/auth-laravel-v1
php artisan vendor:publish --tag=auth-once-off-installation
php artisan vendor:publish --tag=auth-every-update-force-clones --force
php artisan migrate ?????
```

### Manual changes

### `.env` file:

Add (replacing credentials with your actual Auth0 details):

```env
AUTH0_DOMAIN=XXXX.au.auth0.com
AUTH0_CLIENT_ID=XXXXXXXXXXXXXXXX
AUTH0_CLIENT_SECRET=XXXXXXXXXXXX
```

### `.env.example` file:

Add (generic Auth0 example details):

```env
AUTH0_DOMAIN=XXXX.au.auth0.com
AUTH0_CLIENT_ID=XXXXXXXXXXXXXXXX
AUTH0_CLIENT_SECRET=XXXXXXXXXXXX
```

### `config/auth.php` file:

Replace the default 'eloquent' users provider with our 'auth0 provider (***`User Providers` section***):

```php
    'providers' => [
        'users' => [
            'driver' => 'auth0',
            'model' => App\Models\User::class,
        ],
        // 'users' => [
        //     'driver' => 'eloquent',
        //     'model' => App\Models\User::class,
        // ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],
```

Also make sure that the  ***`Authentication Guards`*** section is using the **`'users'`** provider in the **`'web'`** guard :

```php
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        ...
    ],
```

### Only for our **existing projects**: - need to remove on installation:

Need to remove on installation:

* `routes/web.php` - Auth0 stuff
* `config/app.php` - Auth0\Login\LoginServiceProvider::class,
* `app/Providers/AuthServiceProvider.php` - all defined-permission stuff
* `app/Providers/AppServiceProvider.php` - Auth0 + CustomUserRepository bindings

### Troubleshooting

* If there are routing problems use this commands to make sure routes are working properly after installation:

  ```bash
  php artisan route:list
  ```

* Also you can check `bootstrap/cache/services.php`
* If the project is giving you an `Invalid State` error, clean the cache of your browser

## Updating the package

```bash
composer update faithfm/auth-laravel-v1
php artisan vendor:publish --tag=auth-every-update-force-clones --force
```

## Usage

* Define permissions your app will use (in `Repositories/AuthPermissionsRepository.php`).

* Add relevant permissions for each of your users (in the "user_permissions" table).
  * Note: the 'restrictions' column is a JSON field that can be used to define specific restrictions/qualifications to a privilege.  Ie: our Media project uses 'filter' and 'fields' to restrict users to editing specific files/fields.

### Laravel back-end

In the backend check for permissions in the same way you would any other gate - ie:

Simple permission checks:

```php
Gate::allows('use-app');            // simple test  (???untested)
Gate::authorize('use-app');         // route definitions
$this->middleware('can:use-app');   // controller constructors
@can('use-app')                     // blade templates
```

More complex restrictions check/filtering:  (no actual examples but will be something like - TOTALLY UNTESTED - probably should create helper class like we have in front-end)

```php
if (Gate::allows('use-app'))
  if (auth()->user()->permissions->restrictions['file'] == 'restrictedfile')
    // ALLOW/DENY STUFF FROM HAPPENING
```

### Vue front-end

If user permissions are passed from back-end to front-end using our "global javascript `LaravelAppGlobals` variable passed from Blade file" design pattern, a provided `LaravelUserPermissions.js` helper library provides two functions to check permissions.  (It assumes the existence of the global `LaravelAppGlobals.user.permissions` property):

Simple permission checks:

```javascript
import { laravelUserCan } from "../LaravelUserPermissions";
if (laravelUserCan("use-app"))
  // ALLOW STUFF TO HAPPEN
```

More complex restrictions check/filtering

```javascript
import { laravelUserRestrictions } from "../LaravelUserPermissions";
const restrictions = laravelUserRestrictions("use-app");
if (restrictions.status == "NOT PERMITTED")
  // PREVENT STUFF FROM HAPPENING
if (restrictions.status == "ALL PERMITTED")
  // UNFILTERED ACCESS
if (restrictions.status == "SOME PERMITTED") {
  // PARTIAL/FILTERED ACCESS BASED ON RESTRICTIONS JSON DATA - IE: ASSUMING 'filter' field
  if (currentItem.startsWith(restrictions.filter)
    // DO STUFF IF FILTER ALLOWS
```

## Project Architecture

* Files to be cloned/force-published are found in the "clone" folder - with a structure matching target folders of the target project.

* Assumes that Laravel Auditing on all models.

* In the future, it is anticipated that some variations may be required between projects.  At this time the simplistic cloned/force-publish deploy method will need to be replaced by a more sophisticated approach - ie: using Laravel Traits / parent Classes, etc.  At this time, the number of files in the "clone" folder will reduce and be replaced by new files in the "src" folder.

### NOTES:

* We're manually adding (double-up) lots of packages' ServicesProviders in config/app.php - that are automatically registered by their own composer.json / extras section.  (Ie: AuditingServiceProvider).  Need to audit/cleanup.

* Can we come up with a better package name - AuthLaravel/AuthLaravelServiceProvider / auth-laravel-v1  ?????
