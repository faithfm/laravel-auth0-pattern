# Changelog

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

No unreleased changes

## 4.0.2 - 2024-05-20

Update child packages version to pickup bugfixes

## 4.0.1 - 2024-05-17

Minor documentation improvements and corrections


## 4.0.0 - 2024-05-10

MAJOR v4 update - move away from `auth0/login` and create 3x of our own reusable child packages:

* Moved away from the official `auth0/login` (Auth0 Laravel SDK) in favour of our own simpler auth0 package.  Login/logout/callback route registration is now manually added to routes/**web.php**.
* Split the functionality of the package into 3x child packages which are now dependencies of this package.
* Created a proper packagist compose package `faithfm/laravel-auth0-pattern` (no need for composer VCS references).
* Reverted **route middleware group** names back to Laravel defaults - see [Disambiguation of "Authentication Guard" and "Route Middleware Group" names](disambiguation-auth-guard-vs-middleware-group-names.md).
  * 'web' (Laravel default) --> 'web_group' (v2.2.0) --> 'web' (v4.0.0)
  * 'api' (Laravel default) --> 'api_group' (v2.2.0) --> 'web' (v4.0.0)
* Moved the addition of session-based API middleware from app/Providers/**RouteServiceProvider.php** to app/Http/**Kernel.php**.
* Renamed database migrations when the packages were split.  Manual editing of the *'migrations'* table is required.
* Major rewrite (and cleanup) of documentation.


## 3.0.0 - 2024-04-18

* Major revision of our pattern as we update the auth0/login package from v7.6.0 to v7.12.0  (v7.8.0 was where their major update occurred).
* Update config/auth.php and config/auth0.php to work with new Laravel and Auth0 template settings
* Rename Authentication guards for greater clarity:
  * 'web' (Laravel default) --> 'web_guard' (pre v3.0.0) -> 'ffm-session-guard' (post v3.00)
  * 'api' (Laravel default) --> 'api_guard' (pre v3.0.0) -> 'ffm-token-guard' (post v3.00)
* Move AuthZ "defined permissions" from AuthPermissionList.php to config/auth.php
* Gate logic now defines a single 'after' gate and enables multiple permissions to be checked ie: Gate::allows('view-catalog|edit-catalog')
* Disable most automatic registration settings in config/auth0.php (not compatible with our pattern)
* Add 'ffmRegisterAuthenticationRoutes' setting to config/auth.php to replace 'registerAuthenticationRoutes'.  Increases visibility, provides better control/debugging, and matches Auth0's new config style.
* Update User.php model template to match Auth0 SDK structural changes.
* Update Auth0PatternUserRepository.php to match Auth0 SDK structural changes.
* Merge 'clones/' published folder with 'templates/' folder for simplicity.
* Remove PatchedAuthenticationMiddleware ('auth.patched' middleware) - no longer required due to upstream fixes in Auth0 SDK.
* Implement (temporary hack) PatchedAuthenticationGuard to address Auth0 SDK (v7.8.0) bug. (Ie: InvalidTokenException caused by a blank AUTH0_AUDIENCE / 'opaque' JWT tokens).  See our github issue: https://github.com/auth0/laravel-auth0/issues/454
* Implement our own version of the LoginController to capture 'previous' URL and save it as 'intended' session variable to enable redirect in '/callback' route after login

## 2.2.0 - 2023-04-28

* Create+register+use middleware: 'auth.patched:XXX,YYY' / PatchedAuthenticationMiddleware.php

* Rename for greater clarity:  (and to make it easier to find code when troubleshooting)
  * Middleware groups: 'web' --> 'web_group' and 'api' --> 'api_group'
  * Authentication guards: 'web' --> 'web_guard' and 'api' --> 'api_guard'

Note: 'web_group' renaming will also be required in faithfm/laravel-auth0-pattern / auth0pattern-web.php

* Update our version of clone/config/auth0.php
* Update docs
## 2.1.0 - 2023-04-20

### Update to make compatible when using Laravel 10

* Update clone/config/auth.php after bugfix in auth0/login library (v7.5)

* Add our version of clone/config/auth0.php

* Update composer file

> Note: Be sure to [publish the force clones](docs/installation.md#updating-the-package) when upgrading to this version.

## 2.0.1 - 2022-11-15

### Changed config/auth.php - Add duplicate 'web' guard to avoid errors when accessing API routes

* Major update: PHP 8 / Laravel 9 / auth0/login 7.

## 2.0.0 - 2022-11-08

### Changed

* Major update: PHP 8 / Laravel 9 / auth0/login 7.

## 1.0.9 - 2021-12-17

### Changed

* Update diagrams to reflect v1.07 + v1.08 changes.

## 1.0.8 - 2021-12-16

> WARNING: manual changes are required during this upgrade - see UPDATING THE PACKAGE section in [installation.md](installation.md) for more details.

### Added

* Bring session-file-prevention api_token=XXXX codebase into pattern library:

* Bug-fix: prevent creating a session for every token-based API call (ie: 'api_key=XXXX').

* Note when code was originally written for our Media project, our /api/publicusers API was creating a few session-files every minute and was filling up the server hard disk.  (We found 1.5GB with hundreds of thousands of sessions.)

## 1.0.7 - 2021-12-16

### Added

* Bug Fix: api_token authentication was broken in our projects since using this pattern library - two separate providers were actually needed - for session-vs-token guards.

* config/auth.php - brought this new (bug-fixed) file into our pattern library as an auto-cloning file (for greater consistency)

### Changed

* installation.md - updated

* CHANGELOG.md - had not been updated since project created - brought it up-to-date

## 1.0.6 - 2021-11-15

### Changed

* Bug Fix: replace hasColumns for hasColumn method in edit_user_table Migration

## 1.0.5 - 2021-11-03

MAJOR UPDATE

### Changed

* Move lots of files from clone to core

* Add comprehensive documentation

## 1.0.0 - 2021-09-28

### Added

* Created repo as new composer package from pre-existing source files to improve authentication file consistency across our Faith FM projects
