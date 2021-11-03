# laravel-auth0-pattern

![laravel-auth0-pattern-logo.jpg](doc/../docs/laravel-auth0-pattern-logo.jpg)

An Auth0-based library/pattern for Laravel Authentication and Authorisation:  (developed for Faith FM web projects)

* **AuthN** (Authentication) implemented using **Auth0**
* **AuthZ** (Authorization)  with simple **'user-permissions' table** (combined with Laravel/Vue-JS helper Gates & Checks)

This repo is a PHP Composer package created to improve consistency across our existing Faith FM Laravel+Vue projects.  (Previously we had been trying to maintain multiple copies of these files across multiple projects).

### Background:

* Our Auth0-based authentication was initially based on Auth0's [Laravel Quickstart](https://auth0.com/docs/quickstart/webapp/laravel) (May 2020 version)... including the [Optional: Custom User Handling" section](https://auth0.com/docs/quickstart/webapp/laravel#optional-custom-user-handling).

* ...but the tutorial had errors - see https://github.com/auth0/docs/issues/9002

* ...and even once the errors were corrected, it returned an Auth0User interface (with a copy of the static "getAttributes()" properties from the User model.  (No access to User methods, etc)
* Apart from completely changing the way that User data can be accessed for the currently-authenticated user, the Auth0User class is not compatible with other standard features including Laravel's guards implementation.
* Note: Auth0 are currently looking to rectify this by switching their implementation to use an "Auth0 Trait" (to extend the normal User model instead).  (See https://github.com/auth0/laravel-auth0/pull/165)
* However until this becomes generally available, I've adapted @aaronflorey's code to create our own Auth0PatternUserModelTrait which we add to the User model.   (See https://gist.github.com/aaronflorey/d20f27a2b0475d238e10b46de3bc3eb4)




## Installation

See [installation instructions](docs/installation.md).  

> Note: This Composer library is installed directly from Github (not currently registered with packagist.org).  

## Basic Usage

* Define a simple list of permissions your app will use (in [`Repositories/AuthPermissionList.php`](templates/app/Repositories/AuthPermissionList.php) - templated file).

* Add these permissions for each of your relevant users (in the "user_permissions" table).

* The '*restrictions*' column is a JSON field that can **optionally** be used to define specific restrictions/qualifications to a privilege.  Ie: our Media project uses 'filter' and 'fields' to restrict users to editing specific files/fields.

```JSON
  { "fields":["content","guests"], "filter":"file:sa/*" }
```

## Usage - Laravel back-end

In the backend check for permissions in the same way you would any other gate - ie:

Simple permission checks:

```php
Gate::allows('use-app');            // simple test  (???untested)
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

## Sample code to pass permissions via LaravelAppGlobals to front-end:

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

## Architecture

Compared to other Auth0 PHP code we've seen, Auth0's Laravel library + quickstart introduces an extremely complex (yet flexible) architecture we found very difficult to understand + debug.  We ended up producing a whole set of [documentation](docs/underderstanding-laravel-auth0-authn+authz.md) + [diagrams](docs/laravel-auth0-pattern-diagram.pdf) to help us get our heads around this.  

Hopefully this can be helpful to someone else - whether you're using our library... or simply if you're trying to understand the code from the [Auth0 Laravel Quickstart](https://auth0.com/docs/quickstart/webapp/laravel).

> WARNING: No guarantees are made as to the accuracy of this information.  It was simply our own brain-dump as we tried to decode it all... which then resulted in a number of [diagrams](laravel-auth0-pattern.pdf) to try to provide a simplified perspective.

> NOTE: the diagrams are the most up-to-date resource.  We didn't try to go back and align our other documentation 
 after producing them... OR after RENAMING a few things in the library.

![laravel-auth0-pattern-s2-stucture](doc/../docs/images_diagram/laravel-auth0-pattern-s2-stucture.jpg)


## Future Development

* During initial development we regularly experienced issues with "Invalid State" errors.  (See our Auth0 Community [support request](https://community.auth0.com/t/handling-laravel-callback-exceptions-invalid-state-and-cant-initialize-a-new-session-while-there-is-one-activ/45103)).  While developing this documentation we discovered that the endless loop for the error *“Can’t initialize a new session while there is one active session already”* can be fixed by the following code - which we executed in a debug session... but haven't yet incorporated into our codebase.

```php
    session()->forget('auth0\_\_user')
```

* In the future, it is anticipated that some variations may be required between projects.  At this time the simplistic cloned/force-publish deploy method for Models will need to be replaced by a more sophisticated approach - ie: using Laravel Traits / parent Classes, etc. 

* Auth0's code is not retrieving the 'user_metadata' data during code-exchange.  We have unsuccessfully tried a few things, but moved on to other priorities.  Notes from initial research saved under [documentation / future research](docs/underderstanding-laravel-auth0-authn+authz.md#future-research).

* Could create a migration to remove the (unused) "password_resets" table (as mentioned in the quickstart)... but care needed because this could be destructive if accidentally run against the wrong environment.

## General Notes

* Assumes that Laravel Auditing ([owen-it/laravel-auditing](https://github.com/owen-it/laravel-auditing) package) is applied for all models.

* Files to be cloned/force-published are found in the "clone" folder - with a structure matching target folders of the target project.

