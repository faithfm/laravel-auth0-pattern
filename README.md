# laravel-auth0-pattern

![laravel-auth0-pattern-logo.jpg](doc/../docs/laravel-auth0-pattern-logo.jpg)

An Auth0-based library/pattern for Laravel Authentication and Authorisation.  (Implemented as a PHP Composer (VCS/github) package to improve consistency across our Faith FM Laravel+Vue projects)

* **Session**-based **AuthN** (Authentication) is implemented using an **Auth0** backend (combined with our own User model)
  * Unlike a default Laravel application, this guard is made available for both web and API routes, since our applications do not implement stateless/headless font-ends, thus session-based state is available even for AJAX API calls from our javascript/Vue front ends.

* Simple **token**-based **AuthN** (Authentication) is also available (ie: `?api_token=XXXX`).  This is not related to Auth0.

  * Protection is included to prevent creation of hundreds of session-files when using token-based authentication.
* **AuthZ** (Authorization)  with a simple **'user-permissions' table** is provided (includes Laravel/Vue-JS helper Gates & Checks)

## Background

* The need for our own library/pattern initially arose from the complexity required to use Auth0 in a Laravel app, since the [auth0/login](https://github.com/auth0/laravel-auth0) library (pre-v7.0) did not provide an easy way for auth()->user() to return a genuine User model... and this tends to break compatibility with much of the Laravel ecosystem including Laravel Nova.
* Much of this complexity was resolved in v7.0 of [auth0/login](https://github.com/auth0/laravel-auth0) (v2.0 of our library/pattern), but the Auth0-to-Model connection still requires implementation in a [User Repository](src/Auth0PatternUserRepository.php).
* Major improvements were introduced in 7.8 of [auth0/login](https://github.com/auth0/laravel-auth0) (v3.0 of our library/pattern).  Some things were simplified while others were complicated by automatic registration functionality that included hard-coded aspects not aligning to our pattern.
* ...and **the need for a consist approach across our projects still remains**.

## Structure of this Library / Pattern:

* Uses capabilities from the `auth0/login` (Auth0 Laravel SDK) composer package:
  * ie: Auth0's authentication (AuthN) drivers are used, but NOT Auth0's authorization (AuthZ) drivers, etc

* Publishes the following templates:
  * `config/auth0.php` - intended for use without adjustment
  * ``config/auth.php`  - intended for use without adjustment... **except** for the `'defined_permissions'` setting which is **always updated** with a specific list of **permissions** for each application
    * This config defines our main '***ffm-session-guard***' session-based authentication guard
      * Uses '*auth0.authenticator.patched*'  (a patched version of Auth0's Authentication Guard driver)
      * Uses '*ffm-auth0-user-provider*' (our custom User Repository)
    * This config defines our simple token-based '***ffm-token-guard***'  (for API use)
  * `app/Models/User.php` - **often customised** with extended functionality
  * `app/Models/UserPermission.php` - intended for use without adjustment
* Creates [authorization **gates**](https://laravel.com/docs/master/authorization#gates) for all `'defined_permissions'` in `config/auth.php` 
  * Ie: `middleware('can:use-app')` is successful when a user has been given the '*use-app*' permission in `user_permissions` table
* Registers the following authentication **routes**: */login, /logout, /callback*
  * Similiar to automatic route registration in Auth0 SDK, but applies our 'web_group' middleware instead of the default Laravel 'web' middleware hard-coded in to the Auth0 SDK.
* Registers a patched version of Auth0's Authentication Guard driver
  * Note: this driver is a temporary bug-fix to overcome a current (v7.12) bug where the Auth0 SDK does not correctly handle the 'accessToken' vs 'idToken' when AUTH0_AUDIENCE is blank
* Provides the following migrations:
  * `2021_11_02_231849_edit_users_table.php` 
    * ADD fields:  *sub*, *api_token* 
    * DROP fields:  *password*, *email_verified_at* 
    * DROP unique constraint for field: *email* 

  * `2021_05_31_010233_create_user_permissions_table.php`
    * CREATE table:  *user_permissions*

> [!NOTE]
>
> Both models use Laravel Auditing ([owen-it/laravel-auditing](https://github.com/owen-it/laravel-auditing) package) - a package that is used in all our applications.
>
> Older versions of the Laravel Auth0 SDK were documented thoroughly to enable us to understand the complex structure involved.  This is now totally out-of-date, but can be [viewed here for reference purposes](docs/legacy-notes.md), however no guarantees is made to the accuracy of this information.



## Installation

See [installation instructions](docs/installation.md).  

> Note: This Composer library is installed directly from Github (not currently registered with packagist.org).  

## Basic Usage

* Define a simple list of permissions your app will use (in `config/auth.php` templated file).

* Add these permissions for each of your relevant users (in the "user_permissions" table).

* The '*restrictions*' column is a JSON field that can **optionally** be used to define specific restrictions/qualifications to a privilege.  Ie: our Media project uses 'filter' and 'fields' to restrict users to editing specific files/fields.

```JSON
  { "fields":["content","guests"], "filter":"file:sa/*" }
```

## Usage - Laravel back-end

In the backend, ensure someone is logged-in (AuthN) in same way you would for any other Laravel app - ie:

```php
...->middleware('auth')
# Equivalent to:
...->middleware('auth:ffm-token-guard')   # ie: this is the default authentication guard
```

To use multiple authentication guards the middleware name syntax is:

```php
...->middleware('auth:ffm-token-guard,ffm-session-guard')
```

Check for **permissions** (AuthZ) in the same way you would any other gate - ie:

Simple permission checks:

```php
Gate::allows('use-app');            // simple test
Gate::allows('use-app|edit-posts'); // multiple (ORed) permissions can be checked too
Gate::authorize('use-app');         // route definitions
$this->middleware('can:use-app');   // controller constructors
@can('use-app')                     // blade templates
```

More complex restrictions-field checking/filtering has currently only been implemented in the front-end (see next section)... but in the mean-time you could probably use something like this:   (UNTESTED)

```php
if (Gate::allows('use-app'))
  if (auth()->user()->permissions->restrictions['file'] == 'restrictedfile')
    // ALLOW/DENY STUFF FROM HAPPENING
```

## Usage - Vue front-end

`LaravelUserPermissions.js` is a helper library that allows permission-checks to be performed in the front-end.  

This helper assumes that user permissions are passed from back-end to front-end using a global javascript `LaravelAppGlobals` variable (which is usually passed by the Blade file).  Specifically it is looking for the existence of the global `LaravelAppGlobals.user.permissions` property.

Simple permission checks use the `laravelUserCan()` function:

```javascript
import { laravelUserCan } from "../LaravelUserPermissions";
if (laravelUserCan("use-app"))
  // ALLOW STUFF TO HAPPEN
```

More complex restrictions checks/filtering uses the `laravelUserRestrictions()` function:

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

In the '*restrictions*' field example from our Media project above, the *restrictions* object returned by the `laravelUserRestrictions()` function would have been:

```javascript
{
  status: "SOME PERMITTED",
  fields: ["content","guests"], 
  filter: "file:sa/*"
}
```

The value of the *status* field will be:

* `NOT PERMITTED` - if the requested permission (ie: "use-app") does not exist for the user.
* `ALL PERMITTED` - if the requested permission does exist... AND the *'restrictions'* field is blank.
* `SOME PERMITTED` - if the requested permission does exist... AND the *'restrictions'* field contains valid JSON data.

The remaining fields (ie: *fields* and *filter* in this example) are directly copied from the *'restrictions'* JSON data in the database.

> REMINDER: according to good security practice you should not rely only upon front-end checks to enforce security, but should perform security checks in the back-end too.

## Sample code to pass permissions via LaravelAppGlobals to front-end

```php
  $LaravelAppGlobals = [
    'user' => auth()->user(),     # THIS IS THE IMPORTANT ONE
    'guest' => auth()->guest(),
    'other-stuff' => $myStuff,
    ...
  ];
  return view('media')->with('LaravelAppGlobals', $LaravelAppGlobals);
```

```html
<!doctype html>
<head>
    <!-- Scripts -->
    <script>
        var LaravelAppGlobals = Object.freeze({!! json_encode($LaravelAppGlobals) !!});
    </script>
...
```

## Usage in different packages that require auth

* To allow the `owen-it/laravel-auditing` package to use the new guards you should replace in `config/audit.php`. Replace the guards use by the user

```diff
    'user' => [
        'morph_prefix' => 'user',
        'guards' => [
-           'web',
-           'api',
+           'ffm-session-guard',
+           'ffm-token-guard',
        ],
    ],
```

* Change `config/larecipe.php`

```diff
//Documentation Routes

'docs' => [
        'route' => '/docs',
        'path' => '/resources/docs',
        'landing' => 'sched-editor-colour-schemes',
-       'middleware' => ['web'],
+       'middleware' => ['web_group'],
    ],
   ...
   'settings' => [
        'auth' => false,
        'guard' => null,
        'ga_id' => '',
        'middleware' => [
-           'web',
+           'web_group',
        ],

```

* Change `config/nova.php`

```diff
... 
//Nova Route Middleware
'middleware' => [
-       'web',
+       'web_group',
        HandleInertiaRequests::class,
        DispatchServingNovaEvent::class,
        BootTools::class,
    ],
```

