# Disambiguation of "Authentication Guard" and "Route Middleware Group" names

Laravel unfortunately re-uses the names *'web'* and *'api'* for both "Authentication Guards" and "Route Middleware Groups".

* Authentication Guards are defined in config/**auth.php**, and used whenever specific (non-default) guards are specified (ie: `...-->middleware('auth:web,api')`.
* Middleware Groups are defined in app/Http/**Kernel.php** and used when loading route files in app/Providers/**RouteServiceProvider.php**.

This creates quite a bit of confusion, and furthermore the brevity (and lack of specificity) of these names makes it very difficult to find where they have been used throughout a project.

* **Authentication Guards**:  Our pattern uses non-standard names for greater clarity and disambiguation:
  * **'web'** (Laravel default) --> **'web_guard'** (v2.2.0) --> **'ffm-session-guard'** (v3.0.0)    [based on SessionGuard driver]

  * **'api'** (Laravel default) --> **'api_guard'** (v2.2.0) --> **'ffm-token-guard'** (v3.0.0)    [based on TokenGuard driver]

* **Route Middleware Groups**:  For a time our pattern used non-standard names for greater clarity and disambiguation.  However Laravel 11 completely restructures how these are applied and makes it more difficult to retain non-standard names, so we have reverted to standard naming conventions for middleware groups once again.

  * **'web'** (Laravel default) --> **'web_group'** (v2.2.0) --> **'web'** (v4.0.0)

  * **'api'** (Laravel default) --> **'api_group'** (v2.2.0) --> **'web'** (v4.0.0)

Apart from remembering to rename these in our own codebase, these names often appear in config files for the [external packages](external-package-config.md) we use.

