<?php

use Illuminate\Support\Facades\Route;
use Auth0\Laravel\Controllers\LogoutController;
use Auth0\Laravel\Controllers\CallbackController;
// use Auth0\Laravel\Controllers\LoginController;               // Disabled Auth0 login controller...
use FaithFM\Auth0Pattern\Http\Controllers\LoginController;      // ...replaced with our own version - which captures the 'previous' URL and saves it as the 'intended' URL

// Note: we had to disable Auth0's automatic route registration in the config/auth0.php file (ie: 'registerAuthenticationRoutes' => false)...
// ... since we are using our own 'web_group' group... and Auth0 is hard-coded to use the 'web' middleware group.

Route::group(['middleware' => 'web_group'], static function (): void {
  Route::get('/login', LoginController::class)->name('login');                          // Remember '/login' uses our own special controller
  Route::match(['get', 'post'], '/logout', LogoutController::class)->name('logout');
  Route::get('/callback', CallbackController::class)->name('callback');
});
