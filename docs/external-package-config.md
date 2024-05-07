# Configuration of External Packages

Additional configuration may be required to enable external packages to use our pattern.  The most common settings that need to be adjusted are:

* authentication guard names
* middleware group names     *(no longer required since v4.0.0)*

For more information see [Disambiguation of "Authentication Guard" and "Route Middleware Group" names](disambiguation-auth-guard-vs-middleware-group-names.md).

Typical settings for our more-commonly-used packages are documented below.



## Authentication Guard Names:

### owen-it/laravel-auditing

Modify `config/audit.php`:

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


