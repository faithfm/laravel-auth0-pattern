# Additional Manual Changes (for **existing projects** only):

The following additional steps were required for our projects that had utilised Auth0's Laravel login library.

## BEFORE installation:

### 1. Delete file(s):

* `database/migrations/XXXX_create_user_permissions_table.php`

### 2. Remove legacy code from file(s):

* `routes/web.php` - remove the Auth0 routes (login/logout/callback/profile), as these will be automatically defined in `auth0pattern-web.php`.

* `config/app.php` - remove the 'providers' entry for **Auth0\Login\LoginServiceProvider::class,**, as these are automatically registered by the Auth0 library (and were a double-up).

* `app/Providers/AppServiceProvider.php` - in the `register()` method remove the code that binds *Auth0UserRepository* --> *CustomUserRepository*, as this is now handled in our library's `Auth0PatternServiceProvider.php` file.

* `composer.json` - preferably remove the `auth0/login` from the "require" section (since it's included in our library).


## AFTER installation:

Defined permissions need to be moved from `app/Providers/AuthServiceProvider.php` to the newly-templated file:

* Remove the gate definition `foreach(...)` section in the `boot()` method.
* Move the list of "defined-permission"s to the new `Repositories/AuthPermissionList.php` file

## Troubleshooting:

* If there are routing problems use this command to make sure routes are working properly after installation:

  ```bash
  php artisan route:list
  ```

* Also you can check `bootstrap/cache/services.php`
* If the project is giving you an `Invalid State` error, clean the cache of your browser
