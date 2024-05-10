# Laravel 11 web+api middleware:

For **Laravel 11** onwards, modify  `bootstrap/app.php` instead, to replace the `StartSession` middleware for WEB routes and add the (optional) session-based middleware for API routes:   (NOT FULLY TESTED YET)

```diff
 ->withMiddleware(function (Middleware $middleware) {
 
+    // Replace Laravel default StartSession class with FaithFM\SimpleAuthTokens StartSessino class - which prevents creation of (numerous) session files for requests containing 'api_token=XXXX'  (ie: clients without support for cookies will normally result in creation of a session-file for every API call - potentially resulting in hundreds/thousands of session-files)
+    $middleware->web(replace: [
+        \Illuminate\Session\Middleware\StartSession::class => \FaithFM\SimpleAuthTokens\Http\Middleware\StartSession::class,
+    ]);

+    // OPTIONAL session-related middleware for API routes - recommended by FaithFM\SimpleAuthTokens
+    $middleware->api(append: [
+        \App\Http\Middleware\EncryptCookies::class,
+        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
+        \FaithFM\SimpleAuthTokens\Http\Middleware\StartSession::class,		// FaithFM\SimpleAuthTokens class
+        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
+        \App\Http\Middleware\VerifyCsrfToken::class,
+    ]);
 
```


